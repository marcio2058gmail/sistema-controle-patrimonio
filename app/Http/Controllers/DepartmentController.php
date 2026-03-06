<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreDepartmentRequest;
use App\Http\Requests\UpdateDepartmentRequest;
use App\Models\Department;
use App\Models\Responsibility;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DepartmentController extends Controller
{
    public function index(): View
    {
        $departments = Department::forCompany()
            ->withCount('employees')
            ->orderBy('nome')
            ->paginate(15);

        return view('departments.index', compact('departments'));
    }

    public function create(Request $request): View
    {
        abort_unless($request->user()->isAdmin(), 403);

        return view('departments.create');
    }

    public function store(StoreDepartmentRequest $request): RedirectResponse
    {
        abort_unless($request->user()->isAdmin(), 403);

        Department::create(array_merge($request->validated(), [
            'empresa_id' => session('empresa_ativa_id'),
        ]));

        return redirect()->route('departments.index')
            ->with('sucesso', 'Departamento criado com sucesso.');
    }

    public function show(Department $department): View
    {
        $department->load([
            'employees.responsibilities' => fn ($q) => $q->whereNull('data_devolucao')->with('assets'),
        ]);

        // Estatísticas do departamento
        $totalEmployees    = $department->employees->count();
        $totalAssetsInUse = $department->employees->sum(
            fn ($f) => $f->responsibilities->count()
        );

        // Patrimônios por funcionário para a tabela de uso
        $employeesWithAssets = $department->employees->filter(
            fn ($f) => $f->responsibilities->isNotEmpty()
        );

        return view('departments.show', compact(
            'department',
            'totalEmployees',
            'totalAssetsInUse',
            'employeesWithAssets'
        ));
    }

    public function edit(Request $request, Department $department): View
    {
        abort_unless($request->user()->isAdmin(), 403);

        return view('departments.edit', compact('department'));
    }

    public function update(UpdateDepartmentRequest $request, Department $department): RedirectResponse
    {
        abort_unless($request->user()->isAdmin(), 403);

        $department->update($request->validated());

        return redirect()->route('departments.index')
            ->with('sucesso', 'Departamento atualizado com sucesso.');
    }

    public function destroy(Request $request, Department $department): RedirectResponse
    {
        abort_unless($request->user()->isAdmin(), 403);

        // Desvincula funcionários antes de excluir (nullOnDelete já cuida no DB, mas garantimos)
        $department->employees()->update(['departamento_id' => null]);
        $department->delete();

        return redirect()->route('departments.index')
            ->with('sucesso', 'Departamento excluído. Funcionários desvinculados.');
    }
}
