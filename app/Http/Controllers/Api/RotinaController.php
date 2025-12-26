<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Rotina;
use Exception;
use Illuminate\Http\Request;

class RotinaController extends Controller
{
    public function index()
    {
        try {
            $rotinas = Rotina::where('user_id', auth()->id())
                ->where('ativo', true)
                ->orderBy('tipo')
                ->orderBy('horario')
                ->get();
            
            return response()->json([
                'status' => true,
                'data' => $rotinas,
                'message' => 'Lista de rotinas'
            ], 200);
        } catch (Exception $err) {
            return response()->json([
                'status' => false,
                'message' => 'Não foi possível listar as rotinas',
                'error' => $err->getMessage()
            ], 400);
        }
    }

    public function store(Request $request)
    {
        try {
            $validatedData = $request->validate([
                'nome' => 'required|string|max:255',
                'descricao' => 'nullable|string',
                'tipo' => 'nullable|in:diaria,semanal,mensal',
                'dias_semana' => 'nullable|array',
                'horario' => 'nullable|date_format:H:i',
                'ativo' => 'nullable|boolean',
            ]);
            
            $validatedData['user_id'] = auth()->id();
            if (!isset($validatedData['tipo'])) {
                $validatedData['tipo'] = 'diaria';
            }
            if (!isset($validatedData['ativo'])) {
                $validatedData['ativo'] = true;
            }
            
            $rotina = Rotina::create($validatedData);
            
            return response()->json([
                'status' => true,
                'rotina' => $rotina,
                'message' => 'Rotina criada com sucesso'
            ], 200);
        } catch (Exception $err) {
            return response()->json([
                'status' => false,
                'message' => 'Não foi possível criar a rotina',
                'error' => $err->getMessage()
            ], 400);
        }
    }

    public function show(string $id)
    {
        try {
            $rotina = Rotina::where('user_id', auth()->id())->findOrFail($id);
            
            return response()->json([
                'status' => true,
                'data' => $rotina,
                'message' => 'Rotina encontrada'
            ], 200);
        } catch (Exception $err) {
            return response()->json([
                'status' => false,
                'message' => 'Rotina não encontrada',
                'error' => $err->getMessage()
            ], 404);
        }
    }

    public function update(Request $request, string $id)
    {
        try {
            $rotina = Rotina::where('user_id', auth()->id())->findOrFail($id);
            
            $validatedData = $request->validate([
                'nome' => 'sometimes|string|max:255',
                'descricao' => 'nullable|string',
                'tipo' => 'sometimes|in:diaria,semanal,mensal',
                'dias_semana' => 'nullable|array',
                'horario' => 'nullable|date_format:H:i',
                'ativo' => 'sometimes|boolean',
            ]);
            
            $rotina->update($validatedData);
            
            return response()->json([
                'status' => true,
                'rotina' => $rotina,
                'message' => 'Rotina atualizada com sucesso'
            ], 200);
        } catch (Exception $err) {
            return response()->json([
                'status' => false,
                'message' => 'Não foi possível atualizar a rotina',
                'error' => $err->getMessage()
            ], 400);
        }
    }

    public function destroy(string $id)
    {
        try {
            $rotina = Rotina::where('user_id', auth()->id())->findOrFail($id);
            $rotina->delete();
            
            return response()->json([
                'status' => true,
                'message' => 'Rotina deletada com sucesso'
            ], 200);
        } catch (Exception $err) {
            return response()->json([
                'status' => false,
                'message' => 'Não foi possível deletar a rotina',
                'error' => $err->getMessage()
            ], 400);
        }
    }
}
