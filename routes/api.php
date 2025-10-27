<?php

use App\Http\Controllers\Api\AnotacaoController;
use App\Http\Controllers\Api\ContaController;
use App\Http\Controllers\Api\CriptoController;
use App\Http\Controllers\Api\CustoController;
use App\Http\Controllers\Api\DietaController;
use App\Http\Controllers\Api\LembreteController;
use App\Http\Controllers\Api\ReceitaController;
use App\Http\Controllers\Api\TopicoAnotacaoController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::prefix('notes')->group(function () {
    Route::get('/', [AnotacaoController::class, 'index'])->name('notes.index');
    Route::get('/{id}', [AnotacaoController::class, 'show'])->name('notes.show');
    Route::post('/', [AnotacaoController::class, 'store'])->name('notes.store');
    Route::put('/{id}', [AnotacaoController::class, 'update'])->name('notes.update');
    Route::delete('/{id}', [AnotacaoController::class, 'destroy'])->name('notes.destroy');
});

Route::prefix('topic')->group(function () {
    Route::get('/', [TopicoAnotacaoController::class, 'index'])->name('topic.index');
    Route::get('/{id}', [TopicoAnotacaoController::class, 'show'])->name('topic.show');
    Route::post('/', [TopicoAnotacaoController::class, 'store'])->name('topic.store');
    Route::put('/{id}', [TopicoAnotacaoController::class, 'update'])->name('topic.update');
    Route::delete('/{id}', [TopicoAnotacaoController::class, 'destroy'])->name('topic.destroy');
});

Route::prefix('lembrete')->group(function () {
    Route::get('/', [LembreteController::class, 'index'])->name('lembrete.index');
    Route::get('/{id}', [LembreteController::class, 'show'])->name('lembrete.show');
    Route::post('/', [LembreteController::class, 'store'])->name('lembrete.store');
    Route::put('/{id}', [LembreteController::class, 'update'])->name('lembrete.update');
    Route::delete('/{id}', [LembreteController::class, 'destroy'])->name('lembrete.destroy');
});

Route::prefix('dieta')->group(function () {
    Route::get('/', [DietaController::class, 'index'])->name('dieta.index');
    Route::get('/{id}', [DietaController::class, 'show'])->name('dieta.show');
    Route::post('/', [DietaController::class, 'store'])->name('dieta.store');
    Route::post('/think-ai', [DietaController::class, 'processByAi'])->name('dieta.process');
    Route::put('/{id}', [DietaController::class, 'update'])->name('dieta.update');
    Route::delete('/{id}', [DietaController::class, 'destroy'])->name('dieta.destroy');
});

Route::prefix('conta')->group(function () {
    Route::get('/', [ContaController::class, 'index'])->name('conta.index');
    Route::get('/{id}', [ContaController::class, 'show'])->name('conta.show');
    Route::post('/', [ContaController::class, 'store'])->name('conta.store');
    Route::put('/{id}', [ContaController::class, 'update'])->name('conta.update');
    Route::delete('/{id}', [ContaController::class, 'destroy'])->name('conta.destroy');
});

Route::prefix('cripto')->group(function () {
    Route::get('/', [CriptoController::class, 'index'])->name('cripto.index');
    Route::get('/{id}', [CriptoController::class, 'show'])->name('cripto.show');
    Route::post('/', [CriptoController::class, 'store'])->name('cripto.store');
    Route::put('/{id}', [CriptoController::class, 'update'])->name('cripto.update');
    Route::delete('/{id}', [CriptoController::class, 'destroy'])->name('cripto.destroy');
});

Route::prefix('receita')->group(function () {
    Route::get('/', [ReceitaController::class, 'index'])->name('receita.index');
    Route::get('/{id}', [ReceitaController::class, 'show'])->name('receita.show');
    Route::post('/', [ReceitaController::class, 'store'])->name('receita.store');
    Route::put('/{id}', [ReceitaController::class, 'update'])->name('receita.update');
    Route::delete('/{id}', [ReceitaController::class, 'destroy'])->name('receita.destroy');
});

Route::prefix('custo')->group(function () {
    Route::get('/', [CustoController::class, 'index'])->name('custo.index');
    Route::get('/{id}', [CustoController::class, 'show'])->name('custo.show');
    Route::post('/', [CustoController::class, 'store'])->name('custo.store');
    Route::put('/{id}', [CustoController::class, 'update'])->name('custo.update');
    Route::delete('/{id}', [CustoController::class, 'destroy'])->name('custo.destroy');
});

// Route::get('/anotacoes', [TopicoAnotacaoController::class, 'show'])->name('anotacoes.show');
// Route::get('/anotacoes/{anotacao}', [AnotacaoController::class, 'view'])->name('anotacoes.view');
// Route::post('/anotacoes', [AnotacaoController::class, 'update'])->name('anotacoes.update');