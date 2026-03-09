<?php

namespace App\Http\Controllers;

use App\Models\Company;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CompanyController extends Controller
{
    // -------------------------------------------------------
    // Seleção de empresa (todos os usuários autenticados)
    // -------------------------------------------------------

    /**
     * Tela de seleção de empresa após login.
     */
    public function select(Request $request): View
    {
        $user    = $request->user();
        $empresas = $user->isSuperAdmin()
            ? Company::where('ativa', true)->orderBy('nome')->get()
            : $user->empresas()->where('ativa', true)->orderBy('nome')->get();

        return view('companies.select', compact('empresas'));
    }

    /**
     * Armazena a empresa ativa na sessão.
     */
    public function switch(Request $request): RedirectResponse
    {
        $user      = $request->user();
        $empresaId = (int) $request->empresa_id;

        // Super admin pode selecionar 0 = Todas as empresas
        if ($user->isSuperAdmin()) {
            $request->validate(['empresa_id' => ['required', 'integer']]);
            if ($empresaId !== 0) {
                abort_unless(Company::where('id', $empresaId)->exists(), 422, 'Empresa não encontrada.');
            }
        } else {
            $request->validate(['empresa_id' => ['required', 'integer', 'exists:empresas,id']]);
            $permitida = $user->empresas()->where('empresa_id', $empresaId)->exists();
            abort_unless($permitida, 403, 'Acesso não permitido a esta empresa.');
        }

        session(['empresa_ativa_id' => $empresaId ?: null]);

        return redirect()->intended(route('dashboard'));
    }

    // -------------------------------------------------------
    // CRUD de empresas (super_admin apenas)
    // -------------------------------------------------------

    public function index(): View
    {
        $companies = Company::withCount(['employees', 'assets'])
            ->orderBy('nome')
            ->paginate(15);

        // Templates disponíveis: derivados dos arquivos em resources/views/responsibilities/templates/
        $pdfTemplates = collect(glob(resource_path('views/responsibilities/templates/*.blade.php')))
            ->mapWithKeys(fn($path) => [
                pathinfo($path, PATHINFO_FILENAME) => ucfirst(str_replace(['.blade', '-', '_'], ['', ' ', ' '], pathinfo($path, PATHINFO_FILENAME)))
            ]);

        return view('companies.index', compact('companies', 'pdfTemplates'));
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'nome'     => ['required', 'string', 'max:255'],
            'cnpj'     => ['nullable', 'string', 'max:18', 'unique:empresas,cnpj'],
            'telefone' => ['nullable', 'string', 'max:20'],
            'email'    => ['nullable', 'email', 'max:255'],
            'ativa'    => ['boolean'],
        ]);

        $data['ativa'] = $request->boolean('ativa', true);

        Company::create($data);

        return redirect()->route('companies.index')
            ->with('sucesso', 'Empresa criada com sucesso.');
    }

    public function update(Request $request, Company $company): RedirectResponse
    {
        $data = $request->validate([
            'nome'       => ['required', 'string', 'max:255'],
            'cnpj'       => ['nullable', 'string', 'max:18', "unique:empresas,cnpj,{$company->id}"],
            'telefone'   => ['nullable', 'string', 'max:20'],
            'email'      => ['nullable', 'email', 'max:255'],
            'ativa'      => ['boolean'],
            'modelo_pdf' => ['nullable', 'string', 'max:50'],
        ]);

        $data['ativa'] = $request->boolean('ativa', true);
        $data['modelo_pdf'] = $request->input('modelo_pdf', 'padrao');

        $company->update($data);

        return redirect()->route('companies.index')
            ->with('sucesso', 'Empresa atualizada com sucesso.');
    }

    public function destroy(Company $company): RedirectResponse
    {
        $company->delete();

        return redirect()->route('companies.index')
            ->with('sucesso', 'Empresa removida com sucesso.');
    }

    // -------------------------------------------------------
    // Gestão de usuários por empresa (super_admin apenas)
    // -------------------------------------------------------

    public function users(Company $company): View
    {
        $company->load('users');
        $allUsers = User::where('role', '!=', 'super_admin')->orderBy('name')->get();

        return view('companies.users', compact('company', 'allUsers'));
    }

    public function addUser(Request $request, Company $company): RedirectResponse
    {
        $data = $request->validate([
            'user_id' => ['required', 'integer', 'exists:users,id'],
            'role'    => ['required', 'in:admin,manager,employee'],
        ]);

        $company->users()->syncWithoutDetaching([
            $data['user_id'] => ['role' => $data['role']],
        ]);

        return redirect()->route('companies.users', $company)
            ->with('sucesso', 'Usuário adicionado à empresa.');
    }

    public function removeUser(Request $request, Company $company): RedirectResponse
    {
        $request->validate([
            'user_id' => ['required', 'integer', 'exists:users,id'],
        ]);

        $company->users()->detach($request->user_id);

        return redirect()->route('companies.users', $company)
            ->with('sucesso', 'Usuário removido da empresa.');
    }
}
