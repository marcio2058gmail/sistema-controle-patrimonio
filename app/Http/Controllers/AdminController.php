<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreAdminRequest;
use App\Http\Requests\UpdateAdminRequest;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;

class AdminController extends Controller
{
    public function index(): View
    {
        $admins = User::where('role', 'admin')
            ->orderBy('name')
            ->paginate(15);

        return view('admins.index', compact('admins'));
    }

    public function store(StoreAdminRequest $request): RedirectResponse
    {
        User::create([
            'name'              => $request->name,
            'email'             => $request->email,
            'password'          => Hash::make($request->password),
            'email_verified_at' => now(),
            'role'              => 'admin',
        ]);

        return redirect()->route('admins.index')
            ->with('sucesso', 'Administrador criado com sucesso.');
    }

    public function update(UpdateAdminRequest $request, User $admin): RedirectResponse
    {
        abort_if($admin->role !== 'admin', 404);

        $data = [
            'name'  => $request->name,
            'email' => $request->email,
        ];

        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
        }

        $admin->update($data);

        return redirect()->route('admins.index')
            ->with('sucesso', 'Administrador atualizado com sucesso.');
    }

    public function destroy(Request $request, User $admin): RedirectResponse
    {
        abort_unless($request->user()->isAdmin(), 403);
        abort_if($admin->role !== 'admin', 404);
        abort_if($admin->id === $request->user()->id, 403, 'Você não pode remover a si mesmo.');

        $admin->delete();

        return redirect()->route('admins.index')
            ->with('sucesso', 'Administrador removido com sucesso.');
    }
}
