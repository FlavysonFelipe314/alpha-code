<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Tarefa;
use Exception;
use Illuminate\Http\Request;

class TarefaController extends Controller
{
    public function index(Request $request)
    {
        try {
            $colunaId = $request->query('coluna_id');
            $query = Tarefa::where('user_id', auth()->id());
            
            if ($colunaId) {
                $query->where('tarefa_coluna_id', $colunaId);
            }
            
            $tarefas = $query->with('coluna')->orderBy('ordem')->get();
            
            return response()->json([
                'status' => true,
                'data' => $tarefas,
                'message' => 'Lista de tarefas'
            ], 200);
        } catch (Exception $err) {
            return response()->json([
                'status' => false,
                'message' => 'Não foi possível listar as tarefas',
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
                'prioridade' => 'nullable|in:baixa,media,alta',
                'tarefa_coluna_id' => 'required|exists:tarefa_colunas,id',
                'ordem' => 'nullable|integer',
            ]);
            
            $validatedData['user_id'] = auth()->id();
            if (!isset($validatedData['prioridade'])) {
                $validatedData['prioridade'] = 'media';
            }
            if (!isset($validatedData['ordem'])) {
                $maxOrdem = Tarefa::where('tarefa_coluna_id', $validatedData['tarefa_coluna_id'])->max('ordem') ?? 0;
                $validatedData['ordem'] = $maxOrdem + 1;
            }
            
            $tarefa = Tarefa::create($validatedData);
            $tarefa->load('coluna');
            
            return response()->json([
                'status' => true,
                'tarefa' => $tarefa,
                'message' => 'Tarefa criada com sucesso'
            ], 200);
        } catch (Exception $err) {
            return response()->json([
                'status' => false,
                'message' => 'Não foi possível criar a tarefa',
                'error' => $err->getMessage()
            ], 400);
        }
    }

    public function show(string $id)
    {
        try {
            $tarefa = Tarefa::where('user_id', auth()->id())
                ->with(['coluna', 'pomodoros'])
                ->findOrFail($id);
            
            return response()->json([
                'status' => true,
                'data' => $tarefa,
                'message' => 'Tarefa encontrada'
            ], 200);
        } catch (Exception $err) {
            return response()->json([
                'status' => false,
                'message' => 'Tarefa não encontrada',
                'error' => $err->getMessage()
            ], 404);
        }
    }

    public function update(Request $request, string $id)
    {
        try {
            $tarefa = Tarefa::where('user_id', auth()->id())->findOrFail($id);
            
            $validatedData = $request->validate([
                'titulo' => 'sometimes|string|max:255',
                'descricao' => 'nullable|string',
                'prioridade' => 'sometimes|in:baixa,media,alta',
                'tarefa_coluna_id' => 'sometimes|exists:tarefa_colunas,id',
                'ordem' => 'sometimes|integer',
            ]);
            
            $tarefa->update($validatedData);
            $tarefa->load('coluna');
            
            return response()->json([
                'status' => true,
                'tarefa' => $tarefa,
                'message' => 'Tarefa atualizada com sucesso'
            ], 200);
        } catch (Exception $err) {
            return response()->json([
                'status' => false,
                'message' => 'Não foi possível atualizar a tarefa',
                'error' => $err->getMessage()
            ], 400);
        }
    }

    public function destroy(string $id)
    {
        try {
            $tarefa = Tarefa::where('user_id', auth()->id())->findOrFail($id);
            $tarefa->delete();
            
            return response()->json([
                'status' => true,
                'message' => 'Tarefa deletada com sucesso'
            ], 200);
        } catch (Exception $err) {
            return response()->json([
                'status' => false,
                'message' => 'Não foi possível deletar a tarefa',
                'error' => $err->getMessage()
            ], 400);
        }
    }

    public function move(Request $request, string $id)
    {
        try {
            $tarefa = Tarefa::where('user_id', auth()->id())->findOrFail($id);
            
            $validatedData = $request->validate([
                'tarefa_coluna_id' => 'required|exists:tarefa_colunas,id',
                'ordem' => 'nullable|integer',
            ]);
            
            $tarefa->update($validatedData);
            $tarefa->load('coluna');
            
            return response()->json([
                'status' => true,
                'tarefa' => $tarefa,
                'message' => 'Tarefa movida com sucesso'
            ], 200);
        } catch (Exception $err) {
            return response()->json([
                'status' => false,
                'message' => 'Não foi possível mover a tarefa',
                'error' => $err->getMessage()
            ], 400);
        }
    }
}
