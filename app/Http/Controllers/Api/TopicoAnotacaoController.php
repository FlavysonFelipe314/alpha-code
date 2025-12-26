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
            $topicos = TopicoAnotacao::where('user_id', auth()->id())->orderBy('created_at', 'DESC')->get();
            
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
        $validatedData['user_id'] = auth()->id();
        
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
        try {
            $topico = TopicoAnotacao::where('user_id', auth()->id())->findOrFail($id);
            
            $validatedData = $request->validate([
                'name' => 'required|string|max:255'
            ]);
            
            $topico->update($validatedData);
            
            return response()->json([
                'status' => true,
                'user' => $topico, // Mantém 'user' para compatibilidade com o frontend
                'topic' => $topico,
                'data' => $topico,
                'message' => 'Tópico atualizado com sucesso'
            ], 200);
        } catch (Exception $err) {
            return response()->json([
                'status' => false,
                'message' => 'Não foi possível atualizar o tópico',
                'error' => $err->getMessage()
            ], 400);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        try {
            $topico = TopicoAnotacao::where('user_id', auth()->id())->findOrFail($id);
            $topico->delete();
            
            return response()->json([
                'status' => true,
                'message' => 'Tópico deletado com sucesso'
            ], 200);
        } catch (Exception $err) {
            return response()->json([
                'status' => false,
                'message' => 'Não foi possível deletar o tópico',
                'error' => $err->getMessage()
            ], 400);
        }
    }
}
