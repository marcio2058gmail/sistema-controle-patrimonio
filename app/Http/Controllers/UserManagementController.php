<?php

namespace App\Http\Controllers;

use App\Models\Company;
use App\Models\Department;
use App\Models\Employee;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;
use Illuminate\View\View;

class UserManagementController extends Controller
{
    // -------------------------------------------------------
    // Listagem
    // -------------------------------------------------------

    public function index(Request $request): View
    {
        $authUser = $request->user();

        $query = User::with(['employee.department', 'empresas'])
            ->where('role', '!=', 'super_admin')
            ->orderBy('name');

        if ($authUser->isSuperAdmin()) {
            // Super admin: filtra por empresa se solicitado
            if ($request->filled('empresa_id')) {
                $query->whereHas('empresas', fn ($q) => $q->where('empresa_id', $request->empresa_id));
            }
        } else {
            // Admin/Manager: apenas usuários da empresa ativa
            $empresaId = (int) session('empresa_ativa_id');
            if ($empresaId) {
                $query->whereHas('empresas', fn ($q) => $q->where('empresa_id', $empresaId));
            }
        }

        if ($request->filled('role')) {
            $query->where('role', $request->role);
        }

        $users    = $query->paginate(20)->withQueryString();
        $companies  = $authUser->isSuperAdmin()
            ? Company::where('ativa', true)->orderBy('nome')->get()
            : collect();
        $departments = Department::forCompany()->orderBy('nome')->get(['id', 'nome']);

        // Para select de empresa no modal (super_admin vê todas; admin vê só a ativa)
        $companiesForForm = $authUser->isSuperAdmin()
            ? Company::where('ativa', true)->orderBy('nome')->get()
            : Company::where('id', session('empresa_ativa_id'))->get();

        return view('users.index', compact('users', 'companies', 'departments', 'companiesForForm'));
    }

    // -------------------------------------------------------
    // Criação
    // -------------------------------------------------------

    public function store(Request $request): RedirectResponse
    {
        $authUser = $request->user();

        $data = $request->validate($this->storeRules($request));

        if ($authUser->isSuperAdmin()) {
            $empresaIds = array_map('intval', $data['empresa_ids']);
        } else {
            $empresaId = (int) session('empresa_ativa_id');
            abort_unless($empresaId, 422, 'Nenhuma empresa selecionada.');
            $empresaIds = [$empresaId];
        }

        DB::transaction(function () use ($data, $empresaIds) {
            // Cria o usuário
            $user = User::create([
                'name'              => $data['name'],
                'email'             => $data['email'],
                'cpf'               => $data['cpf'] ?? null,
                'password'          => Hash::make($data['password']),
                'email_verified_at' => now(),
                'role'              => $data['role'],
            ]);

            // Vincula a todas as empresas selecionadas
            $pivots = [];
            foreach ($empresaIds as $id) {
                $pivots[$id] = ['role' => $data['role']];
            }
            $user->empresas()->attach($pivots);

            // Para manager e employee: cria registro de funcionário na empresa principal
            if (in_array($data['role'], ['manager', 'employee'])) {
                Employee::create([
                    'user_id'         => $user->id,
                    'nome'            => $data['name'],
                    'email'           => $data['email'],
                    'cargo'           => $data['cargo'] ?? null,
                    'rg_numero'       => $data['rg_numero'] ?? null,
                    'ctps_numero'     => $data['ctps_numero'] ?? null,
                    'ctps_serie'      => $data['ctps_serie'] ?? null,
                    'departamento_id' => $data['departamento_id'] ?? null,
                    'empresa_id'      => $empresaIds[0],
                ]);
            }
        });

        return redirect()->route('users.index')
            ->with('sucesso', 'Usuário cadastrado com sucesso.');
    }

    // -------------------------------------------------------
    // Atualização
    // -------------------------------------------------------

    public function update(Request $request, User $user): RedirectResponse
    {
        abort_if($user->isSuperAdmin(), 403, 'Não é possível editar um super administrador.');

        $authUser = $request->user();

        $data = $request->validate($this->updateRules($request, $user));

        if ($authUser->isSuperAdmin()) {
            $empresaIds = array_map('intval', $data['empresa_ids']);
        } else {
            $empresaId = (int) session('empresa_ativa_id');
            $empresaIds = $empresaId ? [$empresaId] : [];
        }

        DB::transaction(function () use ($data, $user, $empresaIds, $authUser) {
            $userData = [
                'name'  => $data['name'],
                'email' => $data['email'],
                'cpf'   => $data['cpf'] ?? null,
                'role'  => $data['role'],
            ];

            if (!empty($data['password'])) {
                $userData['password'] = Hash::make($data['password']);
            }

            $user->update($userData);

            // Atualiza vínculo com empresa(s)
            if (!empty($empresaIds)) {
                $pivots = [];
                foreach ($empresaIds as $id) {
                    $pivots[$id] = ['role' => $data['role']];
                }
                if ($authUser->isSuperAdmin()) {
                    // Substitui completamente as empresas vinculadas
                    $user->empresas()->sync($pivots);
                } else {
                    $user->empresas()->syncWithoutDetaching($pivots);
                }
            }

            // Atualiza ou cria registro de funcionário
            if (in_array($data['role'], ['manager', 'employee'])) {
                $primaryId = $empresaIds[0] ?? $user->employee?->empresa_id;
                $empData = [
                    'nome'            => $data['name'],
                    'email'           => $data['email'],
                    'cargo'           => $data['cargo'] ?? null,
                    'rg_numero'       => $data['rg_numero'] ?? null,
                    'ctps_numero'     => $data['ctps_numero'] ?? null,
                    'ctps_serie'      => $data['ctps_serie'] ?? null,
                    'departamento_id' => $data['departamento_id'] ?? null,
                    'empresa_id'      => $primaryId,
                ];

                if ($user->employee) {
                    $user->employee->update($empData);
                } else {
                    Employee::create(array_merge($empData, ['user_id' => $user->id]));
                }
            }
        });

        return redirect()->route('users.index')
            ->with('sucesso', 'Usuário atualizado com sucesso.');
    }

    // -------------------------------------------------------
    // Exclusão
    // -------------------------------------------------------

    public function destroy(Request $request, User $user): RedirectResponse
    {
        abort_if($user->isSuperAdmin(), 403, 'Não é possível excluir um super administrador.');
        abort_if($user->id === $request->user()->id, 403, 'Você não pode excluir a si mesmo.');

        DB::transaction(function () use ($user) {
            $user->employee?->delete();
            $user->empresas()->detach();
            $user->delete();
        });

        return redirect()->route('users.index')
            ->with('sucesso', 'Usuário removido com sucesso.');
    }

    // -------------------------------------------------------
    // Regras de validação
    // -------------------------------------------------------

    private function storeRules(Request $request): array
    {
        $isSuperAdmin = $request->user()->isSuperAdmin();

        return array_merge([
            'name'     => ['required', 'string', 'max:150'],
            'email'    => ['required', 'email', 'max:150', 'unique:users,email'],
            'cpf'      => ['nullable', 'string', 'size:14', 'unique:users,cpf'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'role'     => ['required', Rule::in(['admin', 'manager', 'employee'])],
            'cargo'           => ['nullable', 'string', 'max:100'],
            'rg_numero'       => ['nullable', 'string', 'max:20'],
            'ctps_numero'     => ['nullable', 'string', 'max:20'],
            'ctps_serie'      => ['nullable', 'string', 'max:10'],
            'departamento_id' => ['nullable', 'exists:departamentos,id'],
        ], $isSuperAdmin ? [
            'empresa_ids'   => ['required', 'array', 'min:1'],
            'empresa_ids.*' => ['integer', 'exists:empresas,id'],
        ] : []);
    }

    private function updateRules(Request $request, User $user): array
    {
        $isSuperAdmin = $request->user()->isSuperAdmin();

        return array_merge([
            'name'     => ['required', 'string', 'max:150'],
            'email'    => ['required', 'email', 'max:150', Rule::unique('users', 'email')->ignore($user->id)],
            'cpf'      => ['nullable', 'string', 'size:14', Rule::unique('users', 'cpf')->ignore($user->id)],
            'password' => ['nullable', 'string', 'min:8', 'confirmed'],
            'role'     => ['required', Rule::in(['admin', 'manager', 'employee'])],
            'cargo'           => ['nullable', 'string', 'max:100'],
            'rg_numero'       => ['nullable', 'string', 'max:20'],
            'ctps_numero'     => ['nullable', 'string', 'max:20'],
            'ctps_serie'      => ['nullable', 'string', 'max:10'],
            'departamento_id' => ['nullable', 'exists:departamentos,id'],
        ], $isSuperAdmin ? [
            'empresa_ids'   => ['required', 'array', 'min:1'],
            'empresa_ids.*' => ['integer', 'exists:empresas,id'],
        ] : []);
    }
}
