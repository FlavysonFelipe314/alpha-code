@extends('layouts.auth')

@section('title', 'Login')

@section('content')
<div class="text-center mb-8">
    <i class="fas fa-mountain text-4xl text-red-500 mb-4"></i>
    <h1 class="text-3xl font-black text-white mb-2">Bem-vindo de volta</h1>
    <p class="text-neutral-400">Entre na sua conta para continuar</p>
</div>

<form id="login-form" class="space-y-6">
    @csrf
    <div>
        <label for="email" class="block text-sm font-medium text-neutral-300 mb-2">Email</label>
        <input type="email" id="email" name="email" required autofocus
               class="form-input w-full" placeholder="seu@email.com">
    </div>

    <div>
        <label for="password" class="block text-sm font-medium text-neutral-300 mb-2">Senha</label>
        <input type="password" id="password" name="password" required
               class="form-input w-full" placeholder="••••••••">
    </div>

    <div class="flex items-center justify-between">
        <label class="flex items-center">
            <input type="checkbox" name="remember" class="rounded border-gray-300 text-red-600 focus:ring-red-500">
            <span class="ml-2 text-sm text-neutral-400">Lembrar-me</span>
        </label>
        <a href="/forgot-password" class="text-sm link">Esqueceu a senha?</a>
    </div>

    <button type="submit" class="btn-primary">
        Entrar
    </button>

    <div class="text-center text-sm text-neutral-400">
        Não tem uma conta? <a href="/register" class="link">Registre-se</a>
    </div>
</form>

<div id="error-message" class="mt-4 p-3 bg-red-900/50 border border-red-700 rounded text-red-200 text-sm hidden"></div>

@push('scripts')
<script>
document.getElementById('login-form').addEventListener('submit', async (e) => {
    e.preventDefault();
    const formData = new FormData(e.target);
    const errorDiv = document.getElementById('error-message');
    errorDiv.classList.add('hidden');

    try {
        const response = await fetch('/login', {
            method: 'POST',
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || ''
            },
            body: formData
        });

        if (!response.ok) {
            const errorData = await response.json().catch(() => ({ message: 'Erro ao fazer login' }));
            errorDiv.textContent = errorData.message || 'Erro ao fazer login';
            errorDiv.classList.remove('hidden');
            return;
        }

        const data = await response.json();

        if (data.status) {
            window.location.href = data.redirect || '/central-caverna';
        } else {
            errorDiv.textContent = data.message || 'Erro ao fazer login';
            errorDiv.classList.remove('hidden');
        }
    } catch (error) {
        errorDiv.textContent = 'Erro de conexão. Tente novamente.';
        errorDiv.classList.remove('hidden');
    }
});
</script>
@endpush
@endsection

