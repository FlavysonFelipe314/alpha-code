<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreCustoRequest;
use App\Http\Requests\UpdateCustoRequest;
use App\Models\Custo;
use Exception;
use Illuminate\Http\Request;

class CustoController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        try{
            $custos = Custo::with('user')->orderBy('created_at', 'DESC')->get();
            
            return response()->json([
                'status' => true,
                'data' => $custos,
                'message' => 'Lista com todas os custos do usuário'
            ], 200);
        } catch (Exception $err){
            return response()->json([
                'status' => false,
                'message' => 'Não foi possível listar os custos',
                'error' => $err
            ], 400);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreCustoRequest $request)
    {
        try{
            $validatedData = $request->validated();
            $validatedData['user_id'] = 1;

            $custo = Custo::create($validatedData);
            
            return response()->json([
                'status' => true,
                'custo' => $custo,
                'message' => 'Custo cadastrado com sucesso'
            ], 200);

        } catch (Exception $err){
            return response()->json([
                'status' => false,
                'message' => 'Não foi possível cadastrar o custo',
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
            $custo = Custo::with('user')->findOrFail($id);
            
            return response()->json([
                'status' => true,
                'custo' => $custo,
                'message' => 'Custo encontrado com sucesso'
            ], 200);

        } catch (Exception $err){
            return response()->json([
                'status' => false,
                'message' => 'Não foi possível encontrar o custo',
                'error' => $err
            ], 400);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateCustoRequest $request, string $id)
    {
        try{
            $validatedData = $request->validated();

            $custo = Custo::findOrFail($id);
            $custo->update($validatedData);

            return response()->json([
                'status' => true,
                'custo' => $custo->fresh(),
                'message' => 'Custo atualizado com sucesso'
            ], 200);

        } catch (Exception $err){
            return response()->json([
                'status' => false,
                'message' => 'Não foi possível atualizar o custo',
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
            $custo = Custo::findOrFail($id);
            $custo->delete();

            return response()->json([
                'status' => true,
                'message' => 'custo deletado com sucesso'
            ], 200);

        } catch (Exception $err){
            return response()->json([
                'status' => false,
                'message' => 'Não foi possível deletar o custo',
                'error' => $err
            ], 400);
        }
    }
}
