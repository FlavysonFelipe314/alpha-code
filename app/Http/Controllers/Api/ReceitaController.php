<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreReceitaRequest;
use App\Http\Requests\UpdateReceitaRequest;
use App\Models\Receita;
use Exception;
use Illuminate\Http\Request;

class ReceitaController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        try{
            $receitas = Receita::with('user')->orderBy('created_at', 'DESC')->get();
            
            return response()->json([
                'status' => true,
                'data' => $receitas,
                'message' => 'Lista com todas as receitas do usuário'
            ], 200);
        } catch (Exception $err){
            return response()->json([
                'status' => false,
                'message' => 'Não foi possível listar as receitas',
                'error' => $err
            ], 400);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreReceitaRequest $request)
    {
        try{
            $validatedData = $request->validated();
            $validatedData['user_id'] = auth()->id();

            $receita = Receita::create($validatedData);
            
            return response()->json([
                'status' => true,
                'receita' => $receita,
                'message' => 'Receita cadastrada com sucesso'
            ], 200);

        } catch (Exception $err){
            return response()->json([
                'status' => false,
                'message' => 'Não foi possível cadastrar a receita',
                'error' => $err
            ], 400);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        try{
            $receita = Receita::with('user')->findOrFail($id);
            
            return response()->json([
                'status' => true,
                'receita' => $receita,
                'message' => 'Receita encontrada com sucesso'
            ], 200);

        } catch (Exception $err){
            return response()->json([
                'status' => false,
                'message' => 'Não foi possível encontrar a receita',
                'error' => $err
            ], 400);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateReceitaRequest $request, string $id)
    {
        try{
            $validatedData = $request->validated();

            $receita = Receita::findOrFail($id);
            $receita->update($validatedData);

            return response()->json([
                'status' => true,
                'receita' => $receita->fresh(),
                'message' => 'Receita atualizada com sucesso'
            ], 200);

        } catch (Exception $err){
            return response()->json([
                'status' => false,
                'message' => 'Não foi possível atualizar a receita',
                'error' => $err
            ], 400);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        try{
            $receita = Receita::findOrFail($id);
            $receita->delete();

            return response()->json([
                'status' => true,
                'message' => 'Receita deletada com sucesso'
            ], 200);

        } catch (Exception $err){
            return response()->json([
                'status' => false,
                'message' => 'Não foi possível deletar a receita',
                'error' => $err
            ], 400);
        }
    }
}
