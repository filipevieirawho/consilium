<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Services\TursoSync;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function index()
    {
        $users = User::orderBy('name')->get();
        return view('usuarios', compact('users'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email',
            'password' => 'required|string|min:3',
            'role' => 'required|in:admin,gestor,representante',
        ]);

        $validated['password'] = Hash::make($validated['password']);
        $validated['email_verified_at'] = now();
        $validated['active'] = true;

        $user = User::create($validated);

        TursoSync::upsertUser($user);

        return redirect()->route('usuarios.index')->with('success', 'Usuário criado com sucesso!');
    }

    public function updateRole(Request $request, User $user)
    {
        $validated = $request->validate([
            'role' => 'required|in:admin,gestor,representante',
        ]);

        $user->update($validated);
        TursoSync::upsertUser($user);

        return response()->json(['message' => 'Role atualizada com sucesso!']);
    }

    public function updateActive(Request $request, User $user)
    {
        $validated = $request->validate([
            'active' => 'required|boolean',
        ]);

        if ($user->id === auth()->id()) {
            return response()->json(['message' => 'Você não pode desativar seu próprio usuário.'], 403);
        }

        $user->update(['active' => $validated['active']]);
        TursoSync::upsertUser($user);

        return response()->json(['message' => 'Status atualizado com sucesso!']);
    }

    public function destroy(User $user)
    {
        if ($user->id === auth()->id()) {
            return redirect()->route('usuarios.index')->with('error', 'Você não pode excluir seu próprio usuário.');
        }

        TursoSync::execute('DELETE FROM users WHERE id = ?', [$user->id]);
        $user->delete();

        return redirect()->route('usuarios.index')->with('success', 'Usuário excluído com sucesso!');
    }
}
