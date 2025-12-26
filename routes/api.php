<?php

use App\Http\Controllers\Api\AnotacaoController;
use App\Http\Controllers\Api\ContaController;
use App\Http\Controllers\Api\CriptoController;
use App\Http\Controllers\Api\CustoController;
use App\Http\Controllers\Api\DietaController;
use App\Http\Controllers\Api\LembreteController;
use App\Http\Controllers\Api\ReceitaController;
use App\Http\Controllers\Api\TopicoAnotacaoController;
use App\Http\Controllers\Api\AgendaController as ApiAgendaController;
use App\Http\Controllers\Api\BibliotecaController as ApiBibliotecaController;
use App\Http\Controllers\Api\ObjetivoController as ApiObjetivoController;
use App\Http\Controllers\Api\RitualController;
use App\Http\Controllers\Api\ModoCavernaController;
use App\Http\Controllers\Api\DesafioCavernaController;
use App\Http\Controllers\Api\PomodoroController;
use App\Http\Controllers\Api\TarefaController;
use App\Http\Controllers\Api\TarefaColunaController;
use App\Http\Controllers\Api\TreinoController as ApiTreinoController;
use App\Http\Controllers\Api\RotinaController as ApiRotinaController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::middleware(['web', 'auth'])->group(function () {
    Route::get('/user', function (Request $request) {
        return $request->user();
    });

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

    Route::prefix('agenda')->group(function () {
        Route::get('/', [ApiAgendaController::class, 'index']);
        Route::get('/{agenda}', [ApiAgendaController::class, 'show']);
        Route::post('/', [ApiAgendaController::class, 'store']);
        Route::put('/{agenda}', [ApiAgendaController::class, 'update']);
        Route::delete('/{agenda}', [ApiAgendaController::class, 'destroy']);
    });

    Route::prefix('biblioteca')->group(function () {
        Route::get('/', [ApiBibliotecaController::class, 'index']);
        Route::get('/{biblioteca}', [ApiBibliotecaController::class, 'show']);
        Route::get('/{biblioteca}/file', [ApiBibliotecaController::class, 'downloadFile'])->name('biblioteca.downloadFile');
        Route::post('/', [ApiBibliotecaController::class, 'store']);
        Route::post('/{biblioteca}', [ApiBibliotecaController::class, 'update']); // For file uploads with FormData
        Route::put('/{biblioteca}', [ApiBibliotecaController::class, 'update']);
        Route::delete('/{biblioteca}', [ApiBibliotecaController::class, 'destroy']);
    });

    Route::prefix('objetivo')->group(function () {
        Route::get('/', [ApiObjetivoController::class, 'index']);
        Route::get('/{objetivo}', [ApiObjetivoController::class, 'show']);
        Route::post('/', [ApiObjetivoController::class, 'store']);
        Route::put('/{objetivo}', [ApiObjetivoController::class, 'update']);
        Route::delete('/{objetivo}', [ApiObjetivoController::class, 'destroy']);
    });

    Route::prefix('ritual')->group(function () {
        Route::get('/', [RitualController::class, 'index']);
        Route::get('/{id}', [RitualController::class, 'show']);
        Route::post('/', [RitualController::class, 'store']);
        Route::put('/{id}', [RitualController::class, 'update']);
        Route::delete('/{id}', [RitualController::class, 'destroy']);
    });

    Route::prefix('modo-caverna')->group(function () {
        Route::get('/', [ModoCavernaController::class, 'show']);
        Route::put('/', [ModoCavernaController::class, 'update']);
    });

    Route::prefix('desafio-caverna')->group(function () {
        Route::get('/', [DesafioCavernaController::class, 'index']);
        Route::get('/{id}', [DesafioCavernaController::class, 'show']);
        Route::post('/', [DesafioCavernaController::class, 'store']);
        Route::put('/{id}', [DesafioCavernaController::class, 'update']);
        Route::delete('/{id}', [DesafioCavernaController::class, 'destroy']);
    });

    Route::prefix('pomodoro')->group(function () {
        Route::get('/', [PomodoroController::class, 'index']);
        Route::get('/{id}', [PomodoroController::class, 'show']);
        Route::post('/', [PomodoroController::class, 'store']);
        Route::put('/{id}', [PomodoroController::class, 'update']);
        Route::delete('/{id}', [PomodoroController::class, 'destroy']);
    });

    Route::prefix('tarefa-coluna')->group(function () {
        Route::get('/', [TarefaColunaController::class, 'index']);
        Route::get('/{id}', [TarefaColunaController::class, 'show']);
        Route::post('/', [TarefaColunaController::class, 'store']);
        Route::put('/{id}', [TarefaColunaController::class, 'update']);
        Route::delete('/{id}', [TarefaColunaController::class, 'destroy']);
    });

    Route::prefix('tarefa')->group(function () {
        Route::get('/', [TarefaController::class, 'index']);
        Route::get('/{id}', [TarefaController::class, 'show']);
        Route::post('/', [TarefaController::class, 'store']);
        Route::put('/{id}', [TarefaController::class, 'update']);
        Route::post('/{id}/move', [TarefaController::class, 'move']);
        Route::delete('/{id}', [TarefaController::class, 'destroy']);
    });

    Route::prefix('treino')->group(function () {
        Route::get('/', [ApiTreinoController::class, 'index']);
        Route::get('/stats', [ApiTreinoController::class, 'stats']);
        Route::post('/think-ai', [ApiTreinoController::class, 'processByAi'])->name('treino.process');
        Route::get('/{id}', [ApiTreinoController::class, 'show']);
        Route::post('/', [ApiTreinoController::class, 'store']);
        Route::put('/{id}', [ApiTreinoController::class, 'update']);
        Route::delete('/{id}', [ApiTreinoController::class, 'destroy']);
    });

           Route::prefix('rotina')->group(function () {
               Route::get('/', [ApiRotinaController::class, 'index']);
               Route::get('/{id}', [ApiRotinaController::class, 'show']);
               Route::post('/', [ApiRotinaController::class, 'store']);
               Route::put('/{id}', [ApiRotinaController::class, 'update']);
               Route::delete('/{id}', [ApiRotinaController::class, 'destroy']);
           });

           Route::prefix('forum/categorias')->group(function () {
               Route::get('/', [\App\Http\Controllers\Api\ForumCategoriaController::class, 'index']);
               Route::get('/{id}', [\App\Http\Controllers\Api\ForumCategoriaController::class, 'show']);
           });

           Route::prefix('forum/posts')->group(function () {
               Route::get('/', [\App\Http\Controllers\Api\ForumPostController::class, 'index']);
               Route::get('/{id}', [\App\Http\Controllers\Api\ForumPostController::class, 'show']);
               Route::post('/', [\App\Http\Controllers\Api\ForumPostController::class, 'store']);
               Route::put('/{id}', [\App\Http\Controllers\Api\ForumPostController::class, 'update']);
               Route::delete('/{id}', [\App\Http\Controllers\Api\ForumPostController::class, 'destroy']);
           });

           Route::prefix('forum/comentarios')->group(function () {
               Route::get('/post/{postId}', [\App\Http\Controllers\Api\ForumComentarioController::class, 'indexByPost']);
               Route::post('/', [\App\Http\Controllers\Api\ForumComentarioController::class, 'store']);
               Route::put('/{id}', [\App\Http\Controllers\Api\ForumComentarioController::class, 'update']);
               Route::delete('/{id}', [\App\Http\Controllers\Api\ForumComentarioController::class, 'destroy']);
           });
       });
