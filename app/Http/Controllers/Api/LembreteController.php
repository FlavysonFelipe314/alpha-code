<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreLembreteRequest;
use App\Http\Requests\UpdateLembreteRequest;
use App\Models\Lembrete;
use Exception;
use Illuminate\Http\Request;

class LembreteController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        try{
            $lembretes = Lembrete::with('user')->orderBy('created_at', 'DESC')->get();

            $message = ($lembretes->count() > 0) ? "Lista com todos os lembretes do usuário" : 'Nenhum Lembrete esta cadastrado'; 

            return response()->json([
                'status' => true,
                'data' => $lembretes,
                'message' => $message
            ], 200);
        } catch (Exception $err){
            return response()->json([
                'status' => false,
                'message' => 'Não foi possível listar os Lembretes',
                'error' => $err
            ], 400);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreLembreteRequest $request)
    {
        $validatedData = $request->validated();
        $validatedData['user_id'] = 1;
        
        try{
            $lembrete = Lembrete::create($validatedData);
            
            return response()->json([
                'status' => true,
                'lembrete' => $lembrete,
                'message' => 'lembrete cadastrado com sucesso'
            ], 200);
        } catch (Exception $err){
            return response()->json([
                'status' => false,
                'message' => 'Não foi possível cadastrar o lembrete',
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
            $lembrete = Lembrete::with('user')->findOrFail($id);
            
            return response()->json([
                'status' => true,
                'lembrete' => $lembrete,
                'message' => 'Lembrete encontrado com sucesso'
            ], 200);
        } catch(Exception $err){
            return response()->json([
                'status' => false,
                'message' => 'Não foi possível encontrar o lembrete',
                'error' => $err
            ], 400);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateLembreteRequest $request, string $id)
    {
        try{
            $lembrete = Lembrete::findOrFail($id);
            $validatedData = $request->validated();

            $lembrete->update($validatedData);

            return response()->json([
                'status' => true,
                'lembrete' => $lembrete->fresh(),
                'message' => 'Lembrete atualizado com sucesso'
            ], 200);
        } catch(Exception $err){
            return response()->json([
                'status' => false,
                'message' => 'Não foi possível atualizar o lembrete',
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
            $lembrete = Lembrete::findOrFail($id);
            
            $lembrete->delete();

            return response()->json([
                'status' => true,
                'message' => 'Lembrete deletado com sucesso'
            ], 200);
        } catch(Exception $err){
            return response()->json([
                'status' => false,
                'message' => 'Não foi possível deletar o lembrete',
                'error' => $err
            ], 400);
        }    
    }
}
