<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreGestorRequest;
use App\Http\Requests\UpdateGestorRequest;
use App\Models\Departamento;
use App\Models\Funcionario;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;

class GestorController extends Controller
{
    public function index(): View
    {
        $gestores = User::where('role', 'gestor')
            ->with('funcionario.departamento')
            ->orderBy('name')
            ->paginate(15);

        return view('gestores.index', compact('gestores'));
    }

    public function create(): View
    {
        $departamentos = Departamento::orderBy('nome')->get();

        return view('gestores.create', compact('departamentos'));
    }

    public function store(StoreGestorRequest $request): RedirectResponse
    {
        DB::transaction(function () use ($request) {
            $user = User::create([
                'name'              => $request->name,
                'email'             => $request->email,
                'password'          => Hash::make($request->password),
                'email_verified_at' => now(),
                'role'              => 'gestor',
            ]);

            Funcionario::create([
                'user_id'         => $user->id,
                'nome'            => $request->name,
                'email'           => $request->email,
                'cargo'           => $request->cargo ?: 'Gestor',
                'departamento_id' => $request->departamento_id ?: null,
            ]);
        });

        return redirect()->route('gestores.index')
            ->with('sucesso', 'Gestor criado com sucesso.');
    }

    public function edit(User $gestor): View
    {
        abort_if($gestor->role !== 'gestor', 404);

        $gestor->load('funcionario.departamento');
        $departamentos = Departamento::orderBy('nome')->get();

        return view('gestores.edit', compact('gestor', 'departamentos'));
    }

    public function update(UpdateGestorRequest $request, User $gestor): RedirectResponse
    {
        abort_if($gestor->role !== 'gestor', 404);

        DB::transaction(function () use ($request, $gestor) {
            $userData = [
                'name'  => $request->name,
                'email' => $request->email,
            ];

            if ($request->filled('password')) {
                $userData['password'] = Hash::make($request->password);
            }

            $gestor->update($userData);

            if ($gestor->funcionario) {
                $gestor->funcionario->update([
                    'nome'            => $request->name,
                    'email'           => $request->email,
                    'cargo'           => $request->cargo ?: 'Gestor',
                    'departamento_id' => $request->departamento_id ?: null,
                ]);
            } else {
                Funcionario::create([
                    'user_id'         => $gestor->id,
                    'nome'            => $request->name,
                    'email'           => $request->email,
                    'cargo'           => $request->cargo ?: 'Gestor',
                    'departamento_id' => $request->departamento_id ?: null,
                ]);
            }
        });

        return redirect()->route('gestores.index')
            ->with('sucesso', 'Gestor atualizado com sucesso.');
    }

    public function destroy(Request $request, User $gestor): RedirectResponse
    {
        abort_unless($request->user()->isAdmin(), 403, 'Apenas o administrador pode remover gestores.');
        abort_if($gestor->role !== 'gestor', 404);

        DB::transaction(function () use ($gestor) {
            $gestor->funcionario?->delete();
            $gestor->delete();
        });

        return redirect()->route('gestores.index')
            ->with('sucesso', 'Gestor removido com sucesso.');
    }
}
