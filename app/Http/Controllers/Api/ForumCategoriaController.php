<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ForumCategoria;
use Illuminate\Http\Request;

class ForumCategoriaController extends Controller
{
    public function index()
    {
        $categorias = ForumCategoria::where('ativo', true)
            ->withCount('posts')
            ->orderBy('ordem')
            ->get();
        
        return response()->json(['status' => true, 'data' => $categorias]);
    }

    public function show(string $id)
    {
        $categoria = ForumCategoria::withCount('posts')->findOrFail($id);
        return response()->json(['status' => true, 'data' => $categoria]);
    }
}