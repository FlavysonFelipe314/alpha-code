<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\TarefaColuna;
use Exception;
use Illuminate\Http\Request;

class TarefaColunaController extends Controller
{
    public function index()
    {
        try {
            $colunas = TarefaColuna::where('user_id', auth()->id())
                ->with('tarefas')
                ->orderBy('ordem')
                ->get();
            
            return response()->json([
                'status' => true,
                'data' => $colunas,
                'message' => 'Lista de colunas'
            ], 200);
        } catch (Exception $err) {
            return response()->json([
                'status' => false,
                'message' => 'Não foi possível listar as colunas',
                'error' => $err->getMessage()
            ], 400);
        }
    }

    public function store(Request $request)
    {
        try {
            $validatedData = $request->validate([
                'nome' => 'required|string|max:255',
                'ordem' => 'nullable|integer',
            ]);
            
            $validatedData['user_id'] = auth()->id();
            if (!isset($validatedData['ordem'])) {
                $maxOrdem = TarefaColuna::where('user_id', auth()->id())->max('ordem') ?? 0;
                $validatedData['ordem'] = $maxOrdem + 1;
            }
            
            $coluna = TarefaColuna::create($validatedData);
            
            return response()->json([
                'status' => true,
                'coluna' => $coluna,
                'message' => 'Coluna criada com sucesso'
            ], 200);
        } catch (Exception $err) {
            return response()->json([
                'status' => false,
                'message' => 'Não foi possível criar a coluna',
                'error' => $err->getMessage()
            ], 400);
        }
    }

    public function show(string $id)
    {
        try {
            $coluna = TarefaColuna::where('user_id', auth()->id())
                ->with('tarefas')
                ->findOrFail($id);
            
            return response()->json([
                'status' => true,
                'data' => $coluna,
                'message' => 'Coluna encontrada'
            ], 200);
        } catch (Exception $err) {
            return response()->json([
                'status' => false,
                'message' => 'Coluna não encontrada',
                'error' => $err->getMessage()
            ], 404);
        }
    }

    public function update(Request $request, string $id)
    {
        try {
            $coluna = TarefaColuna::where('user_id', auth()->id())->findOrFail($id);
            
            $validatedData = $request->validate([
                'nome' => 'sometimes|string|max:255',
                'ordem' => 'sometimes|integer',
            ]);
            
            $coluna->update($validatedData);
            
            return response()->json([
                'status' => true,
                'coluna' => $coluna,
                'message' => 'Coluna atualizada com sucesso'
            ], 200);
        } catch (Exception $err) {
            return response()->json([
                'status' => false,
                'message' => 'Não foi possível atualizar a coluna',
                'error' => $err->getMessage()
            ], 400);
        }
    }

    public function destroy(string $id)
    {
        try {
            $coluna = TarefaColuna::where('user_id', auth()->id())->findOrFail($id);
            $coluna->delete();
            
            return response()->json([
                'status' => true,
                'message' => 'Coluna deletada com sucesso'
            ], 200);
        } catch (Exception $err) {
            return response()->json([
                'status' => false,
                'message' => 'Não foi possível deletar a coluna',
                'error' => $err->getMessage()
            ], 400);
        }
    }
}
