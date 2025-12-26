<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreAnotacaoRequest;
use App\Http\Requests\UpdateAnotacaoRequest;
use App\Models\Anotacao;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

class AnotacaoController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        try{
            $anotacoes = Anotacao::where('user_id', auth()->id())
                ->orderBy('created_at', 'DESC')
                ->get();
            
            return response()->json([
                'status' => true,
                'data' => $anotacoes,
                'message' => 'Lista com todas as anotações do usuário'
            ], 200);
        } catch (Exception $err){
            return response()->json([
                'status' => false,
                'message' => 'Não foi possível listar as anotações',
                'error' => $err->getMessage()
            ], 400);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreAnotacaoRequest $request)
    {
        try {
            // Aceita tanto FormData quanto JSON
            $data = $request->validated();
            $data['user_id'] = auth()->id();
            
            // Upload do arquivo se existir
            if ($request->hasFile('file')) {
                $file = $request->file('file');
                $fileName = time() . '_' . $file->getClientOriginalName();
                $filePath = $file->storeAs('anotacoes', $fileName, 'public');
                
                $data['file_path'] = $filePath;
                $data['file_type'] = $file->getMimeType();
            }
            
            // Se content não foi fornecido, usa string vazia
            if (!isset($data['content'])) {
                $data['content'] = '';
            }

            $anotacao = Anotacao::create($data);
            
            return response()->json([
                'status' => true,
                'anotacao' => $anotacao,
                'message' => 'Anotação cadastrada com sucesso'
            ], 200);
        } catch (Exception $err){
            Log::error('Erro ao criar anotação:', ['error' => $err->getMessage()]);
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
        try {
            $anotacao = Anotacao::where('user_id', auth()->id())->findOrFail($id);
         
            return response()->json([
                'status' => true,
                'anotacao' => $anotacao,
                'message' => 'Anotação encontrada com sucesso'
            ], 200);
        } catch (Exception $err){
            return response()->json([
                'status' => false,
                'message' => 'Não foi possível encontrar a anotação',
                'error' => $err->getMessage()
            ], 400);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateAnotacaoRequest $request, string $id)
    {
        try {
            $anotacao = Anotacao::where('user_id', auth()->id())->findOrFail($id);
            
            // Aceita tanto FormData quanto JSON
            $data = $request->validated();
            
            // Upload do arquivo se existir e for novo
            if ($request->hasFile('file')) {
                // Deleta arquivo antigo se existir
                if ($anotacao->file_path && Storage::disk('public')->exists($anotacao->file_path)) {
                    Storage::disk('public')->delete($anotacao->file_path);
                }
                
                $file = $request->file('file');
                $fileName = time() . '_' . $file->getClientOriginalName();
                $filePath = $file->storeAs('anotacoes', $fileName, 'public');
                
                $data['file_path'] = $filePath;
                $data['file_type'] = $file->getMimeType();
            }
            
            $anotacao->update($data);
         
            return response()->json([
                'status' => true,
                'anotacao' => $anotacao->fresh(),
                'message' => 'Anotação atualizada com sucesso'
            ], 200);
        } catch (Exception $err){
            Log::error('Erro ao atualizar anotação:', ['error' => $err->getMessage()]);
            return response()->json([
                'status' => false,
                'message' => 'Não foi possível atualizar a anotação',
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
            $anotacao = Anotacao::where('user_id', auth()->id())->findOrFail($id);
            
            // Deleta arquivo se existir
            if ($anotacao->file_path && Storage::disk('public')->exists($anotacao->file_path)) {
                Storage::disk('public')->delete($anotacao->file_path);
            }
            
            $anotacao->delete();
            
            return response()->json([
                'status' => true,
                'message' => 'Anotação deletada com sucesso'
            ], 200);
        } catch (Exception $err){
            return response()->json([
                'status' => false,
                'message' => 'Não foi possível deletar a anotação',
                'error' => $err->getMessage()
            ], 400);
        }
    }
}
