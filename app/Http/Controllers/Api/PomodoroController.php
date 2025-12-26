<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Pomodoro;
use Exception;
use Illuminate\Http\Request;

class PomodoroController extends Controller
{
    public function index(Request $request)
    {
        try {
            $data = $request->query('data');
            $query = Pomodoro::where('user_id', auth()->id());
            
            if ($data) {
                $query->whereDate('data', $data);
            }
            
            $pomodoros = $query->with('tarefa')->orderBy('data', 'DESC')->orderBy('created_at', 'DESC')->get();
            
            return response()->json([
                'status' => true,
                'data' => $pomodoros,
                'message' => 'Lista de pomodoros'
            ], 200);
        } catch (Exception $err) {
            return response()->json([
                'status' => false,
                'message' => 'Não foi possível listar os pomodoros',
                'error' => $err->getMessage()
            ], 400);
        }
    }

    public function store(Request $request)
    {
        try {
            $validatedData = $request->validate([
                'data' => 'required|date',
                'duracao_minutos' => 'nullable|integer|min:1',
                'tipo' => 'nullable|in:produtividade,estudos,descanso',
                'concluido' => 'nullable|boolean',
                'tarefa_id' => 'nullable|exists:tarefas,id',
            ]);
            
            $validatedData['user_id'] = auth()->id();
            if (!isset($validatedData['duracao_minutos'])) {
                $validatedData['duracao_minutos'] = 25;
            }
            if (!isset($validatedData['tipo'])) {
                $validatedData['tipo'] = 'produtividade';
            }
            if (!isset($validatedData['concluido'])) {
                $validatedData['concluido'] = false;
            }
            
            $pomodoro = Pomodoro::create($validatedData);
            $pomodoro->load('tarefa');
            
            return response()->json([
                'status' => true,
                'pomodoro' => $pomodoro,
                'message' => 'Pomodoro criado com sucesso'
            ], 200);
        } catch (Exception $err) {
            return response()->json([
                'status' => false,
                'message' => 'Não foi possível criar o pomodoro',
                'error' => $err->getMessage()
            ], 400);
        }
    }

    public function show(string $id)
    {
        try {
            $pomodoro = Pomodoro::where('user_id', auth()->id())
                ->with('tarefa')
                ->findOrFail($id);
            
            return response()->json([
                'status' => true,
                'data' => $pomodoro,
                'message' => 'Pomodoro encontrado'
            ], 200);
        } catch (Exception $err) {
            return response()->json([
                'status' => false,
                'message' => 'Pomodoro não encontrado',
                'error' => $err->getMessage()
            ], 404);
        }
    }

    public function update(Request $request, string $id)
    {
        try {
            $pomodoro = Pomodoro::where('user_id', auth()->id())->findOrFail($id);
            
            $validatedData = $request->validate([
                'data' => 'sometimes|date',
                'duracao_minutos' => 'sometimes|integer|min:1',
                'tipo' => 'sometimes|in:produtividade,estudos,descanso',
                'concluido' => 'sometimes|boolean',
                'tarefa_id' => 'nullable|exists:tarefas,id',
            ]);
            
            $pomodoro->update($validatedData);
            $pomodoro->load('tarefa');
            
            return response()->json([
                'status' => true,
                'pomodoro' => $pomodoro,
                'message' => 'Pomodoro atualizado com sucesso'
            ], 200);
        } catch (Exception $err) {
            return response()->json([
                'status' => false,
                'message' => 'Não foi possível atualizar o pomodoro',
                'error' => $err->getMessage()
            ], 400);
        }
    }

    public function destroy(string $id)
    {
        try {
            $pomodoro = Pomodoro::where('user_id', auth()->id())->findOrFail($id);
            $pomodoro->delete();
            
            return response()->json([
                'status' => true,
                'message' => 'Pomodoro deletado com sucesso'
            ], 200);
        } catch (Exception $err) {
            return response()->json([
                'status' => false,
                'message' => 'Não foi possível deletar o pomodoro',
                'error' => $err->getMessage()
            ], 400);
        }
    }
}
