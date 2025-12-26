<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreContaRequest;
use App\Http\Requests\UpdateContaRequest;
use App\Models\Conta;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;

class ContaController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        try{
            $contas = Conta::with('user')->orderBy('created_at', 'DESC')->get();
            
            return response()->json([
                'status' => true,
                'data' => $contas,
                'message' => 'Lista com todas as contas do usuário'
            ], 200);
        } catch (Exception $err){
            return response()->json([
                'status' => false,
                'message' => 'Não foi possível listar as contas',
                'error' => $err
            ], 400);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreContaRequest $request)
    {
        try{
            $validatedData = $request->validated();
            $validatedData['user_id'] = auth()->id();
            
            // Garantir que observacao seja null se vazio
            if (empty($validatedData['observacao']) || $validatedData['observacao'] === '') {
                $validatedData['observacao'] = null;
            }

            $conta = Conta::create($validatedData);
            
            return response()->json([
                'status' => true,
                'conta' => $conta,
                'message' => 'Conta cadastrada com sucesso'
            ], 200);

        } catch (ValidationException $err) {
            Log::error('Erro de validação ao criar conta:', [
                'errors' => $err->errors(),
                'data' => $request->all()
            ]);
            return response()->json([
                'status' => false,
                'message' => 'Erro de validação',
                'errors' => $err->errors()
            ], 422);
        } catch (Exception $err){
            Log::error('Erro ao criar conta:', [
                'error' => $err->getMessage(),
                'trace' => $err->getTraceAsString(),
                'data' => $request->all()
            ]);
            return response()->json([
                'status' => false,
                'message' => 'Não foi possível cadastrar a conta',
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
            $conta = Conta::with('user')->findOrFail($id);
            
            return response()->json([
                'status' => true,
                'conta' => $conta,
                'message' => 'Conta encontrada com sucesso'
            ], 200);

        } catch (Exception $err){
            return response()->json([
                'status' => false,
                'message' => 'Não foi possível encontrar a conta',
                'error' => $err
            ], 400);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateContaRequest $request, string $id)
    {
        try{
            $validatedData = $request->validated();
            
            // Garantir que observacao seja null se vazio
            if (empty($validatedData['observacao']) || $validatedData['observacao'] === '') {
                $validatedData['observacao'] = null;
            }

            $conta = Conta::findOrFail($id);
            $conta->update($validatedData);

            return response()->json([
                'status' => true,
                'conta' => $conta->fresh(),
                'message' => 'Conta atualizada com sucesso'
            ], 200);

        } catch (ValidationException $err) {
            Log::error('Erro de validação ao atualizar conta:', [
                'errors' => $err->errors(),
                'data' => $request->all()
            ]);
            return response()->json([
                'status' => false,
                'message' => 'Erro de validação',
                'errors' => $err->errors()
            ], 422);
        } catch (Exception $err){
            Log::error('Erro ao atualizar conta:', [
                'error' => $err->getMessage(),
                'trace' => $err->getTraceAsString(),
                'data' => $request->all()
            ]);
            return response()->json([
                'status' => false,
                'message' => 'Não foi possível atualizar a conta',
                'error' => $err->getMessage()
            ], 400);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        try{
            $conta = Conta::findOrFail($id);
            $conta->delete();

            return response()->json([
                'status' => true,
                'message' => 'Conta deletada com sucesso'
            ], 200);

        } catch (Exception $err){
            return response()->json([
                'status' => false,
                'message' => 'Não foi possível deletar a conta',
                'error' => $err
            ], 400);
        }
    }
}
