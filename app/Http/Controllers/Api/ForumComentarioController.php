<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ForumComentario;
use App\Models\ForumPost;
use Illuminate\Http\Request;

class ForumComentarioController extends Controller
{
    public function indexByPost(Request $request, string $postId)
    {
        $comentarios = ForumComentario::where('post_id', $postId)
            ->whereNull('comentario_pai_id')
            ->with(['user', 'respostas.user'])
            ->orderBy('created_at', 'asc')
            ->get();
        
        return response()->json(['status' => true, 'data' => $comentarios]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'post_id' => 'required|exists:forum_posts,id',
            'comentario_pai_id' => 'nullable|exists:forum_comentarios,id',
            'conteudo' => 'required|string',
        ]);
        
        $validated['user_id'] = auth()->id();
        
        $comentario = ForumComentario::create($validated);
        $comentario->load(['user']);
        
        return response()->json(['status' => true, 'data' => $comentario], 201);
    }

    public function update(Request $request, string $id)
    {
        $comentario = ForumComentario::where('user_id', auth()->id())->findOrFail($id);
        
        $validated = $request->validate([
            'conteudo' => 'required|string',
        ]);
        
        $comentario->update($validated);
        $comentario->load(['user']);
        
        return response()->json(['status' => true, 'data' => $comentario]);
    }

    public function destroy(string $id)
    {
        $comentario = ForumComentario::where('user_id', auth()->id())->findOrFail($id);
        $comentario->delete();
        
        return response()->json(['status' => true, 'message' => 'Comentário deletado com sucesso']);
    }
}