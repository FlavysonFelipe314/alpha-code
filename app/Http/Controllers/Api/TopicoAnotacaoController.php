<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreTopicoAnotacaoResquest;
use App\Models\TopicoAnotacao;
use Exception;
use Illuminate\Http\Request;

class TopicoAnotacaoController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        try{
            $topicos = TopicoAnotacao::orderBy('created_at', 'DESC')->get();
            
            return response()->json([
                'status' => true,
                'data' => $topicos,
                'message' => 'Lista com todas os topicos do usuário'
            ], 200);
        } catch (Exception $err){
            return response()->json([
                'status' => false,
                'message' => 'Não foi possível listar os topicos',
                'error' => $err
            ], 400);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreTopicoAnotacaoResquest $request)
    {
        $validatedData = $request->validated();
        $validatedData['user_id'] = 1;
        
        try{
            $user = TopicoAnotacao::create($validatedData);
            
            return response()->json([
                'status' => true,
                'user' => $user,
                'message' => 'Tópico cadastrado com sucesso'
            ], 200);
        } catch (Exception $err){
            return response()->json([
                'status' => false,
                'message' => 'Não foi possível cadastrar o Tópico',
                'error' => $err
            ], 400);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
