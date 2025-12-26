@extends('layouts.auth')

@section('title', 'Recuperar Senha')

@section('content')
<div class="text-center mb-8">
    <i class="fas fa-key text-4xl text-red-500 mb-4"></i>
    <h1 class="text-3xl font-black text-white mb-2">Recuperar Senha</h1>
    <p class="text-neutral-400">Digite seu email para receber o link de recuperação</p>
</div>

<form id="forgot-password-form" class="space-y-6">
    @csrf
    <div>
        <label for="email" class="block text-sm font-medium text-neutral-300 mb-2">Email</label>
        <input type="email" id="email" name="email" required autofocus
               class="form-input w-full" placeholder="seu@email.com">
    </div>

    <button type="submit" class="btn-primary">
        Enviar Link de Recuperação
    </button>

    <div class="text-center text-sm text-neutral-400">
        Lembrou sua senha? <a href="/login" class="link">Voltar ao login</a>
    </div>
</form>

<div id="error-message" class="mt-4 p-3 bg-red-900/50 border border-red-700 rounded text-red-200 text-sm hidden"></div>
<div id="success-message" class="mt-4 p-3 bg-green-900/50 border border-green-700 rounded text-green-200 text-sm hidden"></div>

@push('scripts')
<script>
document.getElementById('forgot-password-form').addEventListener('submit', async (e) => {
    e.preventDefault();
    const formData = new FormData(e.target);
    const errorDiv = document.getElementById('error-message');
    const successDiv = document.getElementById('success-message');
    errorDiv.classList.add('hidden');
    successDiv.classList.add('hidden');

    try {
        const response = await fetch('/forgot-password', {
            method: 'POST',
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || ''
            },
            body: formData
        });

        const data = await response.json();

        if (data.status) {
            successDiv.textContent = data.message || 'Link enviado com sucesso! Verifique seu email.';
            successDiv.classList.remove('hidden');
            e.target.reset();
        } else {
            errorDiv.textContent = data.message || 'Erro ao enviar link';
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




