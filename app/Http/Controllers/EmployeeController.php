<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreEmployeeRequest;
use App\Http\Requests\UpdateEmployeeRequest;
use App\Models\Department;
use App\Models\Employee;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class EmployeeController extends Controller
{
    public function index(): View
    {
        $user = auth()->user();

        $query = Employee::with('department')->latest();

        if ($user->isManager()) {
            $deptId = $user->employee?->departamento_id;
            $query->where('departamento_id', $deptId);
        }

        $employees = $query->paginate(15);
        $departments = Department::orderBy('nome')->get(['id','nome']);
        return view('employees.index', compact('employees', 'departments'));
    }

    public function create(): View
    {
        $departments = Department::orderBy('nome')->get();
        return view('employees.create', compact('departments'));
    }

    public function store(StoreEmployeeRequest $request): RedirectResponse
    {
        Employee::create($request->validated());

        return redirect()->route('employees.index')
            ->with('sucesso', 'Funcionário cadastrado com sucesso.');
    }

    public function show(Employee $employee): View
    {
        $employee->load(['department', 'responsibilities.asset', 'tickets.assets']);
        return view('employees.show', compact('employee'));
    }

    public function edit(Employee $employee): View
    {
        $departments = Department::orderBy('nome')->get();
        return view('employees.edit', compact('employee', 'departments'));
    }

    public function update(UpdateEmployeeRequest $request, Employee $employee): RedirectResponse
    {
        $employee->update($request->validated());

        return redirect()->route('employees.index')
            ->with('sucesso', 'Funcionário atualizado com sucesso.');
    }

    public function destroy(Employee $employee): RedirectResponse
    {
        $employee->delete();

        return redirect()->route('employees.index')
            ->with('sucesso', 'Funcionário removido com sucesso.');
    }
}
