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

        $empresaId = $authUser->isSuperAdmin()
            ? (int) $data['empresa_id']
            : (int) session('empresa_ativa_id');

        abort_unless($empresaId, 422, 'Nenhuma empresa selecionada.');

        DB::transaction(function () use ($data, $empresaId, $authUser) {
            // Cria o usuário
            $user = User::create([
                'name'              => $data['name'],
                'email'             => $data['email'],
                'cpf'               => $data['cpf'] ?? null,
                'password'          => Hash::make($data['password']),
                'email_verified_at' => now(),
                'role'              => $data['role'],
            ]);

            // Vincula à empresa com o papel correto
            $user->empresas()->attach($empresaId, ['role' => $data['role']]);

            // Para manager e employee: cria registro de funcionário
            if (in_array($data['role'], ['manager', 'employee'])) {
                Employee::create([
                    'user_id'         => $user->id,
                    'nome'            => $data['name'],
                    'email'           => $data['email'],
                    'cargo'           => $data['cargo'] ?? null,
                    'departamento_id' => $data['departamento_id'] ?? null,
                    'empresa_id'      => $empresaId,
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

        $empresaId = $authUser->isSuperAdmin()
            ? (int) $data['empresa_id']
            : (int) session('empresa_ativa_id');

        DB::transaction(function () use ($data, $user, $empresaId) {
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

            // Atualiza vinculo com empresa (role no pivot)
            if ($empresaId) {
                $user->empresas()->syncWithoutDetaching([
                    $empresaId => ['role' => $data['role']],
                ]);
            }

            // Atualiza ou cria registro de funcionário
            if (in_array($data['role'], ['manager', 'employee'])) {
                $empData = [
                    'nome'            => $data['name'],
                    'email'           => $data['email'],
                    'cargo'           => $data['cargo'] ?? null,
                    'departamento_id' => $data['departamento_id'] ?? null,
                    'empresa_id'      => $empresaId ?: $user->employee?->empresa_id,
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
            'departamento_id' => ['nullable', 'exists:departamentos,id'],
        ], $isSuperAdmin ? ['empresa_id' => ['required', 'exists:empresas,id']] : []);
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
            'departamento_id' => ['nullable', 'exists:departamentos,id'],
        ], $isSuperAdmin ? ['empresa_id' => ['required', 'exists:empresas,id']] : []);
    }
}
