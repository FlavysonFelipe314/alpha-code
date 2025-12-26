<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class UserController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', \App\Http\Middleware\AdminMiddleware::class]);
    }

    public function index()
    {
        $users = User::orderBy('created_at', 'DESC')->get();
        return response()->json([
            'status' => true,
            'data' => $users
        ], 200);
    }

    public function show($id)
    {
        $user = User::findOrFail($id);
        return response()->json([
            'status' => true,
            'data' => $user
        ], 200);
    }

    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);
        
        $validator = Validator::make($request->all(), [
            'name' => ['sometimes', 'required', 'string', 'max:255'],
            'email' => ['sometimes', 'required', 'string', 'email', 'max:255', 'unique:users,email,' . $id],
            'is_admin' => ['sometimes', 'boolean'],
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => 'Erro de validação',
                'errors' => $validator->errors()
            ], 422);
        }

        $user->update($request->only('name', 'email', 'is_admin'));

        return response()->json([
            'status' => true,
            'message' => 'Usuário atualizado com sucesso!',
            'data' => $user->fresh()
        ], 200);
    }

    public function destroy($id)
    {
        $user = User::findOrFail($id);
        
        // Não permite deletar a si mesmo
        if ($user->id === auth()->id()) {
            return response()->json([
                'status' => false,
                'message' => 'Você não pode deletar sua própria conta'
            ], 400);
        }

        $user->delete();

        return response()->json([
            'status' => true,
            'message' => 'Usuário deletado com sucesso!'
        ], 200);
    }
}
