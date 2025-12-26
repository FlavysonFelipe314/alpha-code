<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Ritual;
use Exception;
use Illuminate\Http\Request;

class RitualController extends Controller
{
    public function index(Request $request)
    {
        try {
            $tipo = $request->query('tipo'); // matinal ou noturno
            $query = Ritual::where('user_id', auth()->id());
            
            if ($tipo) {
                $query->where('tipo', $tipo);
            }
            
            $rituais = $query->orderBy('ordem')->orderBy('horario_inicio')->get();
            
            return response()->json([
                'status' => true,
                'data' => $rituais,
                'message' => 'Lista de rituais'
            ], 200);
        } catch (Exception $err) {
            return response()->json([
                'status' => false,
                'message' => 'Não foi possível listar os rituais',
                'error' => $err->getMessage()
            ], 400);
        }
    }

    public function store(Request $request)
    {
        try {
            $validatedData = $request->validate([
                'nome' => 'required|string|max:255',
                'tipo' => 'required|in:matinal,noturno',
                'horario_inicio' => 'required|date_format:H:i',
                'horario_fim' => 'nullable|date_format:H:i',
                'ordem' => 'nullable|integer',
                'descricao' => 'nullable|string',
                'ativo' => 'nullable|boolean',
            ]);
            
            $validatedData['user_id'] = auth()->id();
            if (!isset($validatedData['ativo'])) {
                $validatedData['ativo'] = true;
            }
            if (!isset($validatedData['ordem'])) {
                $maxOrdem = Ritual::where('user_id', auth()->id())->where('tipo', $validatedData['tipo'])->max('ordem') ?? 0;
                $validatedData['ordem'] = $maxOrdem + 1;
            }
            
            $ritual = Ritual::create($validatedData);
            
            return response()->json([
                'status' => true,
                'ritual' => $ritual,
                'message' => 'Ritual criado com sucesso'
            ], 200);
        } catch (Exception $err) {
            return response()->json([
                'status' => false,
                'message' => 'Não foi possível criar o ritual',
                'error' => $err->getMessage()
            ], 400);
        }
    }

    public function show(string $id)
    {
        try {
            $ritual = Ritual::where('user_id', auth()->id())->findOrFail($id);
            
            return response()->json([
                'status' => true,
                'data' => $ritual,
                'message' => 'Ritual encontrado'
            ], 200);
        } catch (Exception $err) {
            return response()->json([
                'status' => false,
                'message' => 'Ritual não encontrado',
                'error' => $err->getMessage()
            ], 404);
        }
    }

    public function update(Request $request, string $id)
    {
        try {
            $ritual = Ritual::where('user_id', auth()->id())->findOrFail($id);
            
            $validatedData = $request->validate([
                'nome' => 'sometimes|string|max:255',
                'tipo' => 'sometimes|in:matinal,noturno',
                'horario_inicio' => 'sometimes|date_format:H:i',
                'horario_fim' => 'nullable|date_format:H:i',
                'ordem' => 'nullable|integer',
                'descricao' => 'nullable|string',
                'ativo' => 'nullable|boolean',
            ]);
            
            $ritual->update($validatedData);
            
            return response()->json([
                'status' => true,
                'ritual' => $ritual,
                'message' => 'Ritual atualizado com sucesso'
            ], 200);
        } catch (Exception $err) {
            return response()->json([
                'status' => false,
                'message' => 'Não foi possível atualizar o ritual',
                'error' => $err->getMessage()
            ], 400);
        }
    }

    public function destroy(string $id)
    {
        try {
            $ritual = Ritual::where('user_id', auth()->id())->findOrFail($id);
            $ritual->delete();
            
            return response()->json([
                'status' => true,
                'message' => 'Ritual deletado com sucesso'
            ], 200);
        } catch (Exception $err) {
            return response()->json([
                'status' => false,
                'message' => 'Não foi possível deletar o ritual',
                'error' => $err->getMessage()
            ], 400);
        }
    }
}
