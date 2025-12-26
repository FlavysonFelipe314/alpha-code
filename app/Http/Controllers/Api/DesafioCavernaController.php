<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\DesafioCaverna;
use Exception;
use Illuminate\Http\Request;

class DesafioCavernaController extends Controller
{
    public function index()
    {
        try {
            $desafios = DesafioCaverna::where('user_id', auth()->id())
                ->orderBy('created_at', 'DESC')
                ->get();
            
            return response()->json([
                'status' => true,
                'data' => $desafios,
                'message' => 'Lista de desafios'
            ], 200);
        } catch (Exception $err) {
            return response()->json([
                'status' => false,
                'message' => 'Não foi possível listar os desafios',
                'error' => $err->getMessage()
            ], 400);
        }
    }

    public function store(Request $request)
    {
        try {
            $validatedData = $request->validate([
                'titulo' => 'required|string|max:255',
                'descricao' => 'nullable|string',
                'data_inicio' => 'required|date',
                'data_fim' => 'nullable|date',
                'status' => 'nullable|in:pendente,em_andamento,concluido,cancelado',
                'metas' => 'nullable|array',
                'progresso' => 'nullable|array',
            ]);
            
            $validatedData['user_id'] = auth()->id();
            if (!isset($validatedData['status'])) {
                $validatedData['status'] = 'pendente';
            }
            
            $desafio = DesafioCaverna::create($validatedData);
            
            return response()->json([
                'status' => true,
                'desafio' => $desafio,
                'message' => 'Desafio criado com sucesso'
            ], 200);
        } catch (Exception $err) {
            return response()->json([
                'status' => false,
                'message' => 'Não foi possível criar o desafio',
                'error' => $err->getMessage()
            ], 400);
        }
    }

    public function show(string $id)
    {
        try {
            $desafio = DesafioCaverna::where('user_id', auth()->id())->findOrFail($id);
            
            return response()->json([
                'status' => true,
                'data' => $desafio,
                'message' => 'Desafio encontrado'
            ], 200);
        } catch (Exception $err) {
            return response()->json([
                'status' => false,
                'message' => 'Desafio não encontrado',
                'error' => $err->getMessage()
            ], 404);
        }
    }

    public function update(Request $request, string $id)
    {
        try {
            $desafio = DesafioCaverna::where('user_id', auth()->id())->findOrFail($id);
            
            $validatedData = $request->validate([
                'titulo' => 'sometimes|string|max:255',
                'descricao' => 'nullable|string',
                'data_inicio' => 'sometimes|date',
                'data_fim' => 'nullable|date',
                'status' => 'sometimes|in:pendente,em_andamento,concluido,cancelado',
                'metas' => 'nullable|array',
                'progresso' => 'nullable|array',
            ]);
            
            $desafio->update($validatedData);
            
            return response()->json([
                'status' => true,
                'desafio' => $desafio,
                'message' => 'Desafio atualizado com sucesso'
            ], 200);
        } catch (Exception $err) {
            return response()->json([
                'status' => false,
                'message' => 'Não foi possível atualizar o desafio',
                'error' => $err->getMessage()
            ], 400);
        }
    }

    public function destroy(string $id)
    {
        try {
            $desafio = DesafioCaverna::where('user_id', auth()->id())->findOrFail($id);
            $desafio->delete();
            
            return response()->json([
                'status' => true,
                'message' => 'Desafio deletado com sucesso'
            ], 200);
        } catch (Exception $err) {
            return response()->json([
                'status' => false,
                'message' => 'Não foi possível deletar o desafio',
                'error' => $err->getMessage()
            ], 400);
        }
    }
}
