<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Biblioteca;
use App\Http\Requests\StoreBibliotecaRequest;
use App\Http\Requests\UpdateBibliotecaRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

class BibliotecaController extends Controller
{
    public function index(Request $request)
    {
        try {
            $status = $request->query('status');
            $query = Biblioteca::where('user_id', auth()->id());
            if ($status) $query->where('status', $status);
            
            return response()->json([
                'status' => true,
                'data' => $query->orderBy('id', 'desc')->get()
            ], 200);
        } catch (\Exception $e) {
            Log::error('Erro ao listar biblioteca:', ['error' => $e->getMessage()]);
            return response()->json([
                'status' => false,
                'message' => 'Erro ao listar biblioteca',
                'error' => $e->getMessage()
            ], 400);
        }
    }

    public function store(StoreBibliotecaRequest $request)
    {
        try {
            // Aceita tanto FormData quanto JSON
            $data = $request->all();
            $data['user_id'] = auth()->id();
            
            // Remove o campo 'file' dos dados (será processado separadamente)
            unset($data['file']);
            
            // Upload do arquivo se existir
            if ($request->hasFile('file')) {
                $file = $request->file('file');
                $fileName = time() . '_' . $file->getClientOriginalName();
                $filePath = $file->storeAs('biblioteca', $fileName, 'public');
                
                $data['file_path'] = $filePath;
                $data['file_type'] = $file->getMimeType();
                
                // Determina o tipo baseado na extensão
                $extension = strtolower($file->getClientOriginalExtension());
                if ($extension === 'pdf') {
                    $data['type'] = 'book';
                } elseif (in_array($extension, ['mp4', 'avi', 'mkv', 'mov', 'wmv', 'webm'])) {
                    $data['type'] = 'video';
                }
            }
            
            $item = Biblioteca::create($data);
            
            return response()->json([
                'status' => true,
                'data' => $item,
                'message' => 'Item adicionado à biblioteca com sucesso'
            ], 201);
        } catch (\Exception $e) {
            Log::error('Erro ao criar item na biblioteca:', ['error' => $e->getMessage()]);
            return response()->json([
                'status' => false,
                'message' => 'Erro ao adicionar item à biblioteca',
                'error' => $e->getMessage()
            ], 400);
        }
    }

    public function show(Biblioteca $biblioteca)
    {
        try {
            if ($biblioteca->user_id !== auth()->id()) {
                return response()->json([
                    'status' => false,
                    'message' => 'Item não encontrado'
                ], 404);
            }
            
            return response()->json([
                'status' => true,
                'data' => $biblioteca
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Erro ao buscar item',
                'error' => $e->getMessage()
            ], 400);
        }
    }

    public function update(UpdateBibliotecaRequest $request, Biblioteca $biblioteca)
    {
        try {
            if ($biblioteca->user_id !== auth()->id()) {
                return response()->json([
                    'status' => false,
                    'message' => 'Item não encontrado'
                ], 404);
            }
            
            // Aceita tanto FormData quanto JSON
            $data = $request->all();
            
            // Remove o campo 'file' dos dados se existir (será processado separadamente)
            unset($data['file']);
            
            // Upload do arquivo se existir e for novo
            if ($request->hasFile('file')) {
                // Deleta arquivo antigo se existir
                if ($biblioteca->file_path && Storage::disk('public')->exists($biblioteca->file_path)) {
                    Storage::disk('public')->delete($biblioteca->file_path);
                }
                
                $file = $request->file('file');
                $fileName = time() . '_' . $file->getClientOriginalName();
                $filePath = $file->storeAs('biblioteca', $fileName, 'public');
                
                $data['file_path'] = $filePath;
                $data['file_type'] = $file->getMimeType();
                
                // Determina o tipo baseado na extensão
                $extension = strtolower($file->getClientOriginalExtension());
                if ($extension === 'pdf') {
                    $data['type'] = 'book';
                } elseif (in_array($extension, ['mp4', 'avi', 'mkv', 'mov', 'wmv', 'webm'])) {
                    $data['type'] = 'video';
                }
            }
            
            $biblioteca->update($data);
            
            return response()->json([
                'status' => true,
                'data' => $biblioteca->fresh(),
                'message' => 'Item atualizado com sucesso'
            ], 200);
        } catch (\Exception $e) {
            Log::error('Erro ao atualizar item na biblioteca:', ['error' => $e->getMessage()]);
            return response()->json([
                'status' => false,
                'message' => 'Erro ao atualizar item',
                'error' => $e->getMessage()
            ], 400);
        }
    }

    public function destroy(Biblioteca $biblioteca)
    {
        try {
            if ($biblioteca->user_id !== auth()->id()) {
                return response()->json([
                    'status' => false,
                    'message' => 'Item não encontrado'
                ], 404);
            }
            
            // Deleta arquivo se existir
            if ($biblioteca->file_path && Storage::disk('public')->exists($biblioteca->file_path)) {
                Storage::disk('public')->delete($biblioteca->file_path);
            }
            
            $biblioteca->delete();
            
            return response()->json([
                'status' => true,
                'message' => 'Item removido com sucesso'
            ], 200);
        } catch (\Exception $e) {
            Log::error('Erro ao deletar item da biblioteca:', ['error' => $e->getMessage()]);
            return response()->json([
                'status' => false,
                'message' => 'Erro ao remover item',
                'error' => $e->getMessage()
            ], 400);
        }
    }

    public function downloadFile(Biblioteca $biblioteca)
    {
        try {
            if ($biblioteca->user_id !== auth()->id()) {
                return response()->json([
                    'status' => false,
                    'message' => 'Item não encontrado'
                ], 404);
            }
            
            if (!$biblioteca->file_path || !Storage::disk('public')->exists($biblioteca->file_path)) {
                return response()->json([
                    'status' => false,
                    'message' => 'Arquivo não encontrado'
                ], 404);
            }
            
            return Storage::disk('public')->download($biblioteca->file_path);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Erro ao fazer download',
                'error' => $e->getMessage()
            ], 400);
        }
    }
}
