<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreManagerRequest;
use App\Http\Requests\UpdateManagerRequest;
use App\Models\Department;
use App\Models\Employee;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;

class ManagerController extends Controller
{
    public function index(): View
    {
        $managers = User::where('role', 'manager')
            ->with('employee.department')
            ->orderBy('name')
            ->paginate(15);
        $departments = Department::forCompany()->orderBy('nome')->get(['id','nome']);

        return view('managers.index', compact('managers', 'departments'));
    }

    public function create(): View
    {
        $departments = Department::forCompany()->orderBy('nome')->get();

        return view('managers.create', compact('departments'));
    }

    public function store(StoreManagerRequest $request): RedirectResponse
    {
        DB::transaction(function () use ($request) {
            $user = User::create([
                'name'              => $request->name,
                'email'             => $request->email,
                'password'          => Hash::make($request->password),
                'email_verified_at' => now(),
                'role'              => 'manager',
            ]);

            Employee::create([
                'user_id'         => $user->id,
                'nome'            => $request->name,
                'email'           => $request->email,
                'cargo'           => $request->cargo ?: 'Gestor',
                'departamento_id' => $request->departamento_id ?: null,
                'empresa_id'      => session('empresa_ativa_id'),
            ]);
        });

        return redirect()->route('managers.index')
            ->with('sucesso', 'Gestor criado com sucesso.');
    }

    public function edit(User $manager): View
    {
        abort_if($manager->role !== 'manager', 404);

        $manager->load('employee.department');
        $departments = Department::forCompany()->orderBy('nome')->get();

        return view('managers.edit', compact('manager', 'departments'));
    }

    public function update(UpdateManagerRequest $request, User $manager): RedirectResponse
    {
        abort_if($manager->role !== 'manager', 404);

        DB::transaction(function () use ($request, $manager) {
            $userData = [
                'name'  => $request->name,
                'email' => $request->email,
            ];

            if ($request->filled('password')) {
                $userData['password'] = Hash::make($request->password);
            }

            $manager->update($userData);

            if ($manager->employee) {
                $manager->employee->update([
                    'nome'            => $request->name,
                    'email'           => $request->email,
                    'cargo'           => $request->cargo ?: 'Gestor',
                    'departamento_id' => $request->departamento_id ?: null,
                ]);
            } else {
                Employee::create([
                    'user_id'         => $manager->id,
                    'nome'            => $request->name,
                    'email'           => $request->email,
                    'cargo'           => $request->cargo ?: 'Gestor',
                    'departamento_id' => $request->departamento_id ?: null,
                ]);
            }
        });

        return redirect()->route('managers.index')
            ->with('sucesso', 'Gestor atualizado com sucesso.');
    }

    public function destroy(Request $request, User $manager): RedirectResponse
    {
        abort_unless($request->user()->isAdmin(), 403, 'Apenas o administrador pode remover gestores.');
        abort_if($manager->role !== 'manager', 404);

        DB::transaction(function () use ($manager) {
            $manager->employee?->delete();
            $manager->delete();
        });

        return redirect()->route('managers.index')
            ->with('sucesso', 'Gestor removido com sucesso.');
    }
}
