@extends('layouts.auth')

@section('title', 'Redefinir Senha')

@section('content')
<div class="text-center mb-8">
    <i class="fas fa-lock text-4xl text-red-500 mb-4"></i>
    <h1 class="text-3xl font-black text-white mb-2">Redefinir Senha</h1>
    <p class="text-neutral-400">Digite sua nova senha</p>
</div>

<form id="reset-password-form" class="space-y-6">
    @csrf
    <input type="hidden" name="token" value="{{ $token }}">
    <input type="hidden" name="email" value="{{ $email ?? old('email') }}">

    <div>
        <label for="email" class="block text-sm font-medium text-neutral-300 mb-2">Email</label>
        <input type="email" id="email" name="email" value="{{ $email ?? old('email') }}" required
               class="form-input w-full" readonly>
    </div>

    <div>
        <label for="password" class="block text-sm font-medium text-neutral-300 mb-2">Nova Senha</label>
        <input type="password" id="password" name="password" required autofocus
               class="form-input w-full" placeholder="Mínimo 8 caracteres">
    </div>

    <div>
        <label for="password_confirmation" class="block text-sm font-medium text-neutral-300 mb-2">Confirmar Nova Senha</label>
        <input type="password" id="password_confirmation" name="password_confirmation" required
               class="form-input w-full" placeholder="Confirme sua nova senha">
    </div>

    <button type="submit" class="btn-primary">
        Redefinir Senha
    </button>

    <div class="text-center text-sm text-neutral-400">
        <a href="/login" class="link">Voltar ao login</a>
    </div>
</form>

<div id="error-message" class="mt-4 p-3 bg-red-900/50 border border-red-700 rounded text-red-200 text-sm hidden"></div>

@push('scripts')
<script>
document.getElementById('reset-password-form').addEventListener('submit', async (e) => {
    e.preventDefault();
    const formData = new FormData(e.target);
    const errorDiv = document.getElementById('error-message');
    errorDiv.classList.add('hidden');

    try {
        const response = await fetch('/reset-password', {
            method: 'POST',
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || ''
            },
            body: formData
        });

        const data = await response.json();

        if (data.status) {
            alert('Senha redefinida com sucesso! Redirecionando para o login...');
            window.location.href = '/login';
        } else {
            errorDiv.textContent = data.message || 'Erro ao redefinir senha';
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




