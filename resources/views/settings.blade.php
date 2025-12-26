@extends('layouts.app')

@section('title', 'Configurações')

@push('styles')
<style>
    .card {
        background: rgba(24, 24, 27, 0.8);
        border: 1px solid #3f3f46;
        border-radius: 12px;
        padding: 2rem;
    }
    .form-input {
        background-color: #27272a;
        border: 1px solid #3f3f46;
        border-radius: 0.5rem;
        padding: 0.75rem 1rem;
        color: white;
        width: 100%;
    }
    .form-input:focus {
        outline: none;
        border-color: #ef4444;
        box-shadow: 0 0 0 3px rgba(239, 68, 68, 0.1);
    }
    .btn-primary {
        background: #ef4444;
        color: white;
        padding: 0.75rem 1.5rem;
        border-radius: 0.5rem;
        font-weight: 700;
        border: none;
        cursor: pointer;
        transition: background 0.3s;
    }
    .btn-primary:hover {
        background: #dc2626;
    }
</style>
@endpush

@section('content')
<div class="container mx-auto px-4 py-8">
    <h1 class="text-3xl font-black text-white mb-8">Configurações</h1>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Perfil -->
        <div class="card">
            <h2 class="text-2xl font-bold text-white mb-6">Meu Perfil</h2>
            <form id="profile-form" class="space-y-4">
                @csrf
                <div>
                    <label class="block text-sm font-medium text-neutral-300 mb-2">Nome</label>
                    <input type="text" id="profile-name" value="{{ auth()->user()->name }}" class="form-input" required>
                </div>
                <div>
                    <label class="block text-sm font-medium text-neutral-300 mb-2">Email</label>
                    <input type="email" id="profile-email" value="{{ auth()->user()->email }}" class="form-input" required>
                </div>
                <button type="submit" class="btn-primary w-full">Salvar Alterações</button>
            </form>
        </div>

        <!-- Trocar Senha -->
        <div class="card">
            <h2 class="text-2xl font-bold text-white mb-6">Trocar Senha</h2>
            <form id="password-form" class="space-y-4">
                @csrf
                <div>
                    <label class="block text-sm font-medium text-neutral-300 mb-2">Senha Atual</label>
                    <input type="password" id="current-password" class="form-input" required>
                </div>
                <div>
                    <label class="block text-sm font-medium text-neutral-300 mb-2">Nova Senha</label>
                    <input type="password" id="new-password" class="form-input" required minlength="8">
                </div>
                <div>
                    <label class="block text-sm font-medium text-neutral-300 mb-2">Confirmar Nova Senha</label>
                    <input type="password" id="confirm-password" class="form-input" required minlength="8">
                </div>
                <button type="submit" class="btn-primary w-full">Alterar Senha</button>
            </form>
        </div>

    </div>

    <div id="message" class="mt-4 p-4 rounded hidden"></div>
</div>
@endsection

@push('scripts')
<script>
document.getElementById('profile-form').addEventListener('submit', async (e) => {
    e.preventDefault();
    const response = await fetch('/settings/profile', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        },
        body: JSON.stringify({
            name: document.getElementById('profile-name').value,
            email: document.getElementById('profile-email').value
        })
    });
    const data = await response.json();
    showMessage(data.status, data.message);
});

document.getElementById('password-form').addEventListener('submit', async (e) => {
    e.preventDefault();
    const newPassword = document.getElementById('new-password').value;
    const confirmPassword = document.getElementById('confirm-password').value;
    
    if (newPassword !== confirmPassword) {
        showMessage(false, 'As senhas não coincidem!');
        return;
    }
    
    const response = await fetch('/settings/password', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        },
        body: JSON.stringify({
            current_password: document.getElementById('current-password').value,
            password: newPassword,
            password_confirmation: confirmPassword
        })
    });
    const data = await response.json();
    showMessage(data.status, data.message);
    if (data.status) {
        document.getElementById('password-form').reset();
    }
});

function showMessage(success, text) {
    const msgDiv = document.getElementById('message');
    msgDiv.className = `mt-4 p-4 rounded ${success ? 'bg-green-900/50 border border-green-700 text-green-200' : 'bg-red-900/50 border border-red-700 text-red-200'}`;
    msgDiv.textContent = text;
    msgDiv.classList.remove('hidden');
    setTimeout(() => msgDiv.classList.add('hidden'), 5000);
}
</script>
@endpush

