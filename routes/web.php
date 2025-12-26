<?php

use App\Http\Controllers\CentralCavernaController;
use App\Http\Controllers\ForjaTemploController;
use App\Http\Controllers\OrdemCaosController;
use App\Models\Anotacao;
use Illuminate\Support\Facades\Route;

// Rotas de Autenticação
Route::middleware('guest')->group(function () {
    Route::get('/login', [App\Http\Controllers\Auth\LoginController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [App\Http\Controllers\Auth\LoginController::class, 'login']);
    Route::get('/register', [App\Http\Controllers\Auth\RegisterController::class, 'showRegistrationForm'])->name('register');
    Route::post('/register', [App\Http\Controllers\Auth\RegisterController::class, 'register']);
    Route::get('/forgot-password', [App\Http\Controllers\Auth\ForgotPasswordController::class, 'showLinkRequestForm'])->name('password.request');
    Route::post('/forgot-password', [App\Http\Controllers\Auth\ForgotPasswordController::class, 'sendResetLinkEmail'])->name('password.email');
    Route::get('/reset-password/{token}', [App\Http\Controllers\Auth\ForgotPasswordController::class, 'showResetForm'])->name('password.reset');
    Route::post('/reset-password', [App\Http\Controllers\Auth\ForgotPasswordController::class, 'reset'])->name('password.update');
});

Route::post('/logout', [App\Http\Controllers\Auth\LoginController::class, 'logout'])->name('logout')->middleware('auth');

// Landing Page
Route::get('/', [App\Http\Controllers\WelcomeController::class, 'index'])->name('welcome');

// Rotas protegidas - requerem apenas autenticação
Route::middleware(['auth', 'subscribed'])->group(function () {
    Route::get('/dashboard', function () {
        return redirect('/central-caverna');
    });

    Route::get('/anotacoes', function () {
        return view('welcome');
    })->name('anotacoes');

    Route::get('/dieta', function(){
        return view('dieta');
    });

    Route::get('/financa', function(){
        return view('financas');
    });

    Route::get('/biblioteca', function(){
        return view('biblioteca');
    });

    Route::get('/objetivo', function(){
        return view('objetivos');
    });

    Route::get('/agenda', function(){
        return view('agenda');
    });

    Route::get('/central-caverna', [App\Http\Controllers\CentralCavernaController::class, 'index'])->name('central-caverna');
    Route::get('/treino', [App\Http\Controllers\ForjaTemploController::class, 'index'])->name('treino');
    Route::get('/foco', [App\Http\Controllers\OrdemCaosController::class, 'index'])->name('foco');
    
    Route::get('/forum', [App\Http\Controllers\ForumController::class, 'index'])->name('forum');
    Route::get('/forum/categoria/{id}', [App\Http\Controllers\ForumController::class, 'categoria'])->name('forum.categoria');
    Route::get('/forum/post/{id}', [App\Http\Controllers\ForumController::class, 'post'])->name('forum.post');
    
    // Rotas Admin (não requerem assinatura, apenas admin)
    Route::middleware(['admin'])->group(function () {
        Route::get('/admin/users', function () {
            return view('admin.users');
        })->name('admin.users');
        Route::get('/api/admin/users', [App\Http\Controllers\Admin\UserController::class, 'index'])->name('admin.users.api');
        Route::get('/api/admin/users/{id}', [App\Http\Controllers\Admin\UserController::class, 'show'])->name('admin.users.show');
        Route::put('/api/admin/users/{id}', [App\Http\Controllers\Admin\UserController::class, 'update'])->name('admin.users.update');
        Route::delete('/api/admin/users/{id}', [App\Http\Controllers\Admin\UserController::class, 'destroy'])->name('admin.users.destroy');
        
        Route::get('/admin/planos', [App\Http\Controllers\Admin\PlanoController::class, 'index'])->name('admin.planos');
        Route::get('/api/admin/planos', [App\Http\Controllers\Admin\PlanoController::class, 'getPlanos'])->name('admin.planos.api');
        Route::post('/api/admin/planos', [App\Http\Controllers\Admin\PlanoController::class, 'store'])->name('admin.planos.store');
        Route::put('/api/admin/planos/{id}', [App\Http\Controllers\Admin\PlanoController::class, 'update'])->name('admin.planos.update');
        Route::delete('/api/admin/planos/{id}', [App\Http\Controllers\Admin\PlanoController::class, 'destroy'])->name('admin.planos.destroy');
    });
});

// Settings não requer assinatura (acesso livre para usuários autenticados)
Route::middleware('auth')->group(function () {
    Route::get('/settings', [App\Http\Controllers\SettingsController::class, 'index'])->name('settings');
    Route::post('/settings/profile', [App\Http\Controllers\SettingsController::class, 'updateProfile'])->name('settings.profile');
    Route::post('/settings/password', [App\Http\Controllers\SettingsController::class, 'updatePassword'])->name('settings.password');
});