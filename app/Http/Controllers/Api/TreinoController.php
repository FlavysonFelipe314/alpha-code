<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Services\TreinoService;
use App\Models\Treino;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class TreinoController extends Controller
{
    public function index(Request $request)
    {
        try {
            $data = $request->query('data');
            $query = Treino::where('user_id', auth()->id());
            
            if ($data) {
                $query->whereDate('data', $data);
            }
            
            $treinos = $query->orderBy('data', 'DESC')->orderBy('horario')->get();
            
            return response()->json([
                'status' => true,
                'data' => $treinos,
                'message' => 'Lista de treinos'
            ], 200);
        } catch (Exception $err) {
            return response()->json([
                'status' => false,
                'message' => 'Não foi possível listar os treinos',
                'error' => $err->getMessage()
            ], 400);
        }
    }

    public function store(Request $request)
    {
        try {
            $validatedData = $request->validate([
                'nome' => 'required|string|max:255',
                'data' => 'nullable|date',
                'day' => 'nullable|string|max:20',
                'horario' => 'nullable|string',
                'observacoes' => 'nullable|string',
                'peso_atual' => 'nullable|numeric',
                'objetivo' => 'nullable|string',
                'shape' => 'nullable|string',
                'realizado' => 'nullable|boolean',
            ]);
            
            // Converte horário se necessário (formato HH:MM:SS para HH:MM)
            if (isset($validatedData['horario']) && strlen($validatedData['horario']) > 5) {
                $validatedData['horario'] = substr($validatedData['horario'], 0, 5);
            }
            
            // Se não tiver data, usa a data de hoje
            if (!isset($validatedData['data']) || empty($validatedData['data'])) {
                $validatedData['data'] = now()->toDateString();
            }
            
            $validatedData['user_id'] = auth()->id();
            if (!isset($validatedData['realizado'])) {
                $validatedData['realizado'] = false;
            }
            
            $treino = Treino::create($validatedData);
            
            return response()->json([
                'status' => true,
                'treino' => $treino,
                'message' => 'Treino criado com sucesso'
            ], 200);
        } catch (\Illuminate\Validation\ValidationException $err) {
            Log::error('Erro de validação ao criar treino:', [
                'errors' => $err->errors(),
                'data' => $request->all()
            ]);
            return response()->json([
                'status' => false,
                'message' => 'Erro de validação',
                'errors' => $err->errors()
            ], 422);
        } catch (Exception $err) {
            Log::error('Erro ao criar treino:', [
                'error' => $err->getMessage(),
                'trace' => $err->getTraceAsString(),
                'data' => $request->all()
            ]);
            return response()->json([
                'status' => false,
                'message' => 'Não foi possível criar o treino',
                'error' => $err->getMessage()
            ], 400);
        }
    }

    public function show(string $id)
    {
        try {
            $treino = Treino::where('user_id', auth()->id())->findOrFail($id);
            
            return response()->json([
                'status' => true,
                'data' => $treino,
                'message' => 'Treino encontrado'
            ], 200);
        } catch (Exception $err) {
            return response()->json([
                'status' => false,
                'message' => 'Treino não encontrado',
                'error' => $err->getMessage()
            ], 404);
        }
    }

    public function update(Request $request, string $id)
    {
        try {
            $treino = Treino::where('user_id', auth()->id())->findOrFail($id);
            
            $validatedData = $request->validate([
                'nome' => 'sometimes|string|max:255',
                'data' => 'sometimes|date',
                'day' => 'sometimes|string|max:20',
                'horario' => 'nullable|date_format:H:i',
                'observacoes' => 'nullable|string',
                'peso_atual' => 'nullable|numeric',
                'objetivo' => 'nullable|string',
                'shape' => 'nullable|string',
                'realizado' => 'sometimes|boolean',
            ]);
            
            $treino->update($validatedData);
            
            return response()->json([
                'status' => true,
                'treino' => $treino,
                'message' => 'Treino atualizado com sucesso'
            ], 200);
        } catch (Exception $err) {
            return response()->json([
                'status' => false,
                'message' => 'Não foi possível atualizar o treino',
                'error' => $err->getMessage()
            ], 400);
        }
    }

    public function destroy(string $id)
    {
        try {
            $treino = Treino::where('user_id', auth()->id())->findOrFail($id);
            $treino->delete();
            
            return response()->json([
                'status' => true,
                'message' => 'Treino deletado com sucesso'
            ], 200);
        } catch (Exception $err) {
            return response()->json([
                'status' => false,
                'message' => 'Não foi possível deletar o treino',
                'error' => $err->getMessage()
            ], 400);
        }
    }

    public function stats(Request $request)
    {
        try {
            $treinoMaisRecente = Treino::where('user_id', auth()->id())
                ->orderBy('data', 'DESC')
                ->first();
            
            $stats = [
                'peso_atual' => $treinoMaisRecente?->peso_atual ?? null,
                'objetivo' => $treinoMaisRecente?->objetivo ?? null,
                'shape' => $treinoMaisRecente?->shape ?? null,
            ];
            
            return response()->json([
                'status' => true,
                'data' => $stats,
                'message' => 'Estatísticas de treino'
            ], 200);
        } catch (Exception $err) {
            return response()->json([
                'status' => false,
                'message' => 'Não foi possível buscar as estatísticas',
                'error' => $err->getMessage()
            ], 400);
        }
    }

    public function processByAi(Request $request)
    {
        try {
            $validatedData = $request->input();
            $response = TreinoService::generateWorkoutPlan($validatedData);
            
            // Se a resposta for uma string JSON, tenta parsear
            if (is_string($response)) {
                $parsed = json_decode($response, true);
                if (json_last_error() === JSON_ERROR_NONE && is_array($parsed)) {
                    return response()->json($parsed);
                }
                // Se não conseguir parsear, retorna como está (pode conter markdown ou texto adicional)
                return response()->json(['response' => $response]);
            }
            
            // Se já for array ou objeto, retorna direto
            return response()->json($response);
        } catch (Exception $err) {
            Log::error('Erro ao processar IA de treino:', [
                'error' => $err->getMessage(),
                'trace' => $err->getTraceAsString()
            ]);
            return response()->json([
                'status' => false,
                'message' => 'Erro ao gerar plano de treino',
                'error' => $err->getMessage()
            ], 400);
        }
    }
}
