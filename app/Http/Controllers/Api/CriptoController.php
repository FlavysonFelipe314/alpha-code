<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreCriptoRequest;
use App\Http\Requests\UpdateCriptoRequest;
use App\Models\Cripto;
use Exception;
use Illuminate\Http\Request;

class CriptoController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        try{
            $criptos = Cripto::with('user')->orderBy('created_at', 'DESC')->get();
            
            return response()->json([
                'status' => true,
                'data' => $criptos,
                'message' => 'Lista com todas as criptos do usuário'
            ], 200);
        } catch (Exception $err){
            return response()->json([
                'status' => false,
                'message' => 'Não foi possível listar as criptos',
                'error' => $err
            ], 400);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreCriptoRequest $request)
    {
        try{
            $validatedData = $request->validated();
            $validatedData['user_id'] = 1;

            $cripto = Cripto::create($validatedData);
            
            return response()->json([
                'status' => true,
                'cripto' => $cripto,
                'message' => 'Cripto cadastrada com sucesso'
            ], 200);

        } catch (Exception $err){
            return response()->json([
                'status' => false,
                'message' => 'Não foi possível cadastrar a cripto',
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
            $cripto = Cripto::with('user')->findOrFail($id);
            
            return response()->json([
                'status' => true,
                'cripto' => $cripto,
                'message' => 'Cripto encontrada com sucesso'
            ], 200);

        } catch (Exception $err){
            return response()->json([
                'status' => false,
                'message' => 'Não foi possível encontrar a cripto',
                'error' => $err
            ], 400);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateCriptoRequest $request, string $id)
    {
        try{
            $validatedData = $request->validated();

            $cripto = Cripto::findOrFail($id);
            $cripto->update($validatedData);

            return response()->json([
                'status' => true,
                'cripto' => $cripto->fresh(),
                'message' => 'cripto atualizada com sucesso'
            ], 200);

        } catch (Exception $err){
            return response()->json([
                'status' => false,
                'message' => 'Não foi possível atualizar a cripto',
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
            $cripto = Cripto::findOrFail($id);
            $cripto->delete();

            return response()->json([
                'status' => true,
                'message' => 'cripto deletada com sucesso'
            ], 200);

        } catch (Exception $err){
            return response()->json([
                'status' => false,
                'message' => 'Não foi possível deletar a cripto',
                'error' => $err
            ], 400);
        }
    }
}
