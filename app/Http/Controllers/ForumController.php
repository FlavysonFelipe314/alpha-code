<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ForumCategoria;

class ForumController extends Controller
{
    public function index()
    {
        $categorias = ForumCategoria::where('ativo', true)
            ->withCount('posts')
            ->orderBy('ordem')
            ->get();
        
        // Feed com posts mais recentes de todas as categorias
        $feedPosts = \App\Models\ForumPost::with(['user', 'categoria'])
            ->withCount('todosComentarios')
            ->orderBy('fixado', 'desc')
            ->orderBy('created_at', 'desc')
            ->limit(20)
            ->get();
        
        return view('forum', compact('categorias', 'feedPosts'));
    }

    public function categoria($id)
    {
        $categoria = ForumCategoria::with(['posts' => function($query) {
            $query->with('user')->withCount('todosComentarios')->orderBy('fixado', 'desc')->orderBy('created_at', 'desc');
        }])->findOrFail($id);
        return view('forum.categoria', compact('categoria'));
    }

    public function post($id)
    {
        $post = \App\Models\ForumPost::with(['user', 'categoria', 
            'comentarios' => function($query) {
                $query->whereNull('comentario_pai_id')->with('user')->orderBy('created_at', 'asc');
            },
            'comentarios.respostas' => function($query) {
                $query->with('user')->orderBy('created_at', 'asc');
            }])
            ->findOrFail($id);
        
        // Incrementa visualizações
        $post->incrementarVisualizacao();
        $post->refresh();
        
        return view('forum.post', compact('post'));
    }
}