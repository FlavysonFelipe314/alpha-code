<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ModoCaverna;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;

class ModoCavernaController extends Controller
{
    public function show(Request $request)
    {
        try {
            $modoCaverna = ModoCaverna::firstOrCreate(
                ['user_id' => auth()->id()],
                [
                    'dias_consecutivos' => 0,
                    'ultimo_acesso' => null,
                    'ativo' => true,
                ]
            );
            
            // Atualizar streak se necessário
            $hoje = Carbon::today();
            $ultimoAcesso = $modoCaverna->ultimo_acesso ? Carbon::parse($modoCaverna->ultimo_acesso) : null;
            
            if (!$ultimoAcesso || $ultimoAcesso->lt($hoje)) {
                if ($ultimoAcesso && $ultimoAcesso->diffInDays($hoje) == 1) {
                    // Dia consecutivo
                    $modoCaverna->dias_consecutivos += 1;
                } elseif (!$ultimoAcesso || $ultimoAcesso->diffInDays($hoje) > 1) {
                    // Reset streak
                    $modoCaverna->dias_consecutivos = 1;
                }
                $modoCaverna->ultimo_acesso = $hoje;
                $modoCaverna->save();
            }
            
            return response()->json([
                'status' => true,
                'data' => $modoCaverna,
                'message' => 'Dados do Modo Caverna'
            ], 200);
        } catch (Exception $err) {
            return response()->json([
                'status' => false,
                'message' => 'Não foi possível buscar os dados',
                'error' => $err->getMessage()
            ], 400);
        }
    }

    public function update(Request $request)
    {
        try {
            $modoCaverna = ModoCaverna::firstOrCreate(
                ['user_id' => auth()->id()],
                [
                    'dias_consecutivos' => 0,
                    'ultimo_acesso' => null,
                    'ativo' => true,
                ]
            );
            
            $validatedData = $request->validate([
                'ativo' => 'sometimes|boolean',
            ]);
            
            $modoCaverna->update($validatedData);
            
            return response()->json([
                'status' => true,
                'data' => $modoCaverna,
                'message' => 'Modo Caverna atualizado'
            ], 200);
        } catch (Exception $err) {
            return response()->json([
                'status' => false,
                'message' => 'Não foi possível atualizar',
                'error' => $err->getMessage()
            ], 400);
        }
    }
}
