<?php

use App\Models\Anotacao;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dieta', function(){
    return view('dieta');
});

Route::get('/financa', function(){
    return view('financas');
});