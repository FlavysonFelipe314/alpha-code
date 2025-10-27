<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreAnotacaoRequest;
use App\Http\Requests\UpdateAnotacaoRequest;
use App\Models\Anotacao;
use Exception;
use Illuminate\Http\Request;

class AnotacaoController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        try{
            $anotacoes = Anotacao::orderBy('created_at', 'DESC')->get();
            
            return response()->json([
                'status' => true,
                'data' => $anotacoes,
                'message' => 'Lista com todas as anotações do usuário'
            ], 200);
        } catch (Exception $err){
            return response()->json([
                'status' => false,
                'message' => 'Não foi possível listar as anotações',
                'error' => $err
            ], 400);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreAnotacaoRequest $request)
    {
        $validatedData = $request->validated();
        $validatedData['user_id'] = 1;
        
        try{

            $anotacao = Anotacao::create($validatedData);
            
            if($validatedData['topico_anotacao_id']){
                 
            }

            return response()->json([
                'status' => true,
                'anotacao' => $anotacao,
                'message' => 'Anotação cadastrada com sucesso'
            ], 200);
        } catch (Exception $err){
            return response()->json([
                'status' => false,
                'message' => 'Não foi possível cadastrar a anotação',
                'error' => $err->getMessage()
            ], 400);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        try{
            $anotacao = Anotacao::findOrFail($id);
         
            return response()->json([
                'status' => true,
                'anotacao' => $anotacao,
                'message' => 'Anotação encontrada com sucesso'
            ], 200);
        } catch (Exception $err){
            return response()->json([
                'status' => false,
                'message' => 'Não foi possível encontrar a anotação',
                'error' => $err
            ], 400);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateAnotacaoRequest $request, string $id)
    {
        try{
            $anotacao = Anotacao::findOrFail($id);
            $validatedData = $request->validated(); 
            
            $anotacao->update($validatedData);
         
            return response()->json([
                'status' => true,
                'anotacao' => $anotacao->fresh(),
                'message' => 'Anotação atualizada com sucesso'
            ], 200);
        } catch (Exception $err){
            return response()->json([
                'status' => false,
                'message' => 'Não foi possível atualizar a anotação',
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
            $anotacao = Anotacao::findOrFail($id);
            $anotacao->delete();
            
            return response()->json([
                'status' => true,
                'message' => 'Anotação deletada com sucesso'
            ], 200);
        } catch (Exception $err){
            return response()->json([
                'status' => false,
                'message' => 'Não foi possível deletar a anotação',
                'error' => $err
            ], 400);
        }
    }
}
