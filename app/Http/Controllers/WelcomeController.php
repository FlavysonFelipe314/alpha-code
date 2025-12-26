<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Plano;

class WelcomeController extends Controller
{
    public function index()
    {
        $planos = Plano::where('ativo', true)
            ->orderBy('ordem')
            ->orderBy('preco')
            ->get();
        
        return view('landing', compact('planos'));
    }
}