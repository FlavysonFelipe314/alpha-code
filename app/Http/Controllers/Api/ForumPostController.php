<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ForumPost;
use Illuminate\Http\Request;

class ForumPostController extends Controller
{
    public function index(Request $request)
    {
        $query = ForumPost::with(['user', 'categoria'])
            ->withCount('comentarios')
            ->orderBy('fixado', 'desc')
            ->orderBy('created_at', 'desc');
        
        if ($request->has('categoria_id')) {
            $query->where('categoria_id', $request->categoria_id);
        }
        
        $posts = $query->get();
        
        return response()->json(['status' => true, 'data' => $posts]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'categoria_id' => 'required|exists:forum_categorias,id',
            'titulo' => 'required|string|max:255',
            'conteudo' => 'required|string',
        ]);
        
        $validated['user_id'] = auth()->id();
        
        $post = ForumPost::create($validated);
        $post->load(['user', 'categoria']);
        
        return response()->json(['status' => true, 'data' => $post], 201);
    }

    public function show(string $id)
    {
        $post = ForumPost::with(['user', 'categoria', 'comentarios.user', 'comentarios.respostas.user'])
            ->findOrFail($id);
        
        return response()->json(['status' => true, 'data' => $post]);
    }

    public function update(Request $request, string $id)
    {
        $post = ForumPost::where('user_id', auth()->id())->findOrFail($id);
        
        $validated = $request->validate([
            'titulo' => 'sometimes|required|string|max:255',
            'conteudo' => 'sometimes|required|string',
        ]);
        
        $post->update($validated);
        $post->load(['user', 'categoria']);
        
        return response()->json(['status' => true, 'data' => $post]);
    }

    public function destroy(string $id)
    {
        $post = ForumPost::where('user_id', auth()->id())->findOrFail($id);
        $post->delete();
        
        return response()->json(['status' => true, 'message' => 'Post deletado com sucesso']);
    }
}