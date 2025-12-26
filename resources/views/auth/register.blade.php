@extends('layouts.auth')

@section('title', 'Registro')

@section('content')
<div class="text-center mb-8">
    <i class="fas fa-mountain text-4xl text-red-500 mb-4"></i>
    <h1 class="text-3xl font-black text-white mb-2">Criar Conta</h1>
    <p class="text-neutral-400">Registre-se para começar</p>
</div>

<form id="register-form" class="space-y-6">
    @csrf
    <div>
        <label for="name" class="block text-sm font-medium text-neutral-300 mb-2">Nome</label>
        <input type="text" id="name" name="name" required autofocus
               class="form-input w-full" placeholder="Seu nome">
    </div>

    <div>
        <label for="email" class="block text-sm font-medium text-neutral-300 mb-2">Email</label>
        <input type="email" id="email" name="email" required
               class="form-input w-full" placeholder="seu@email.com">
    </div>

    <div>
        <label for="password" class="block text-sm font-medium text-neutral-300 mb-2">Senha</label>
        <input type="password" id="password" name="password" required
               class="form-input w-full" placeholder="Mínimo 8 caracteres">
    </div>

    <div>
        <label for="password_confirmation" class="block text-sm font-medium text-neutral-300 mb-2">Confirmar Senha</label>
        <input type="password" id="password_confirmation" name="password_confirmation" required
               class="form-input w-full" placeholder="Confirme sua senha">
    </div>

    <button type="submit" class="btn-primary">
        Criar Conta
    </button>

    <div class="text-center text-sm text-neutral-400">
        Já tem uma conta? <a href="/login" class="link">Faça login</a>
    </div>
</form>

<div id="error-message" class="mt-4 p-3 bg-red-900/50 border border-red-700 rounded text-red-200 text-sm hidden"></div>

@push('scripts')
<script>
document.getElementById('register-form').addEventListener('submit', async (e) => {
    e.preventDefault();
    const formData = new FormData(e.target);
    const errorDiv = document.getElementById('error-message');
    errorDiv.classList.add('hidden');

    try {
        const response = await fetch('/register', {
            method: 'POST',
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || ''
            },
            body: formData
        });

        const data = await response.json();

        if (data.status) {
            window.location.href = '/central-caverna';
        } else {
            const errorText = data.errors ? Object.values(data.errors).flat().join(', ') : (data.message || 'Erro ao registrar');
            errorDiv.textContent = errorText;
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




