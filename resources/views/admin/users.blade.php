@extends('layouts.app')

@section('title', 'Gerenciar Usuários')

@push('styles')
<style>
    .card {
        background: rgba(24, 24, 27, 0.8);
        border: 1px solid #3f3f46;
        border-radius: 12px;
        padding: 1.5rem;
    }
    .user-card {
        background: rgba(24, 24, 27, 0.8);
        border: 1px solid #3f3f46;
        border-radius: 12px;
        padding: 1.5rem;
        transition: all 0.3s;
    }
    .user-card:hover {
        border-color: #ef4444;
        transform: translateY(-2px);
    }
    .btn-primary {
        background: #ef4444;
        color: white;
        padding: 0.5rem 1rem;
        border-radius: 0.5rem;
        font-weight: 700;
        border: none;
        cursor: pointer;
        transition: background 0.3s;
    }
    .btn-primary:hover {
        background: #dc2626;
    }
    .badge {
        display: inline-block;
        padding: 0.25rem 0.75rem;
        border-radius: 9999px;
        font-size: 0.75rem;
        font-weight: 600;
    }
    .badge-admin {
        background: #ef4444;
        color: white;
    }
    .badge-user {
        background: #3f3f46;
        color: #a1a1aa;
    }
</style>
@endpush

@section('content')
<div class="container mx-auto px-4 py-8">
    <h1 class="text-3xl font-black text-white mb-8">Gerenciar Usuários</h1>

    <div id="users-list" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        <!-- Usuários serão carregados aqui -->
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', async () => {
    await loadUsers();
});

async function loadUsers() {
    const response = await fetch('/api/admin/users');
    const data = await response.json();
    
    if (data.status && data.data) {
        const container = document.getElementById('users-list');
        container.innerHTML = data.data.map(user => `
            <div class="user-card">
                <div class="flex justify-between items-start mb-4">
                    <div>
                        <h3 class="text-xl font-bold text-white">${user.name}</h3>
                        <p class="text-sm text-neutral-400">${user.email}</p>
                    </div>
                    <span class="badge ${user.is_admin ? 'badge-admin' : 'badge-user'}">
                        ${user.is_admin ? 'Admin' : 'Usuário'}
                    </span>
                </div>
                <div class="text-xs text-neutral-500 mb-4">
                    Criado em: ${new Date(user.created_at).toLocaleDateString('pt-BR')}
                </div>
                <div class="flex space-x-2">
                    <button onclick="toggleAdmin(${user.id}, ${!user.is_admin})" class="btn-primary flex-1">
                        ${user.is_admin ? 'Remover Admin' : 'Tornar Admin'}
                    </button>
                    ${user.id !== CURRENT_USER_ID ? `
                        <button onclick="deleteUser(${user.id})" class="btn-primary bg-red-700 hover:bg-red-800">
                            <i class="fas fa-trash"></i>
                        </button>
                    ` : ''}
                </div>
            </div>
        `).join('');
    }
}

async function toggleAdmin(userId, isAdmin) {
    const response = await fetch(`/api/admin/users/${userId}`, {
        method: 'PUT',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        },
        body: JSON.stringify({ is_admin: isAdmin })
    });
    
    if (response.ok) {
        await loadUsers();
    } else {
        alert('Erro ao atualizar usuário');
    }
}

async function deleteUser(userId) {
    if (!confirm('Tem certeza que deseja deletar este usuário?')) return;
    
    const response = await fetch(`/api/admin/users/${userId}`, {
        method: 'DELETE',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        }
    });
    
    if (response.ok) {
        await loadUsers();
    } else {
        alert('Erro ao deletar usuário');
    }
}
</script>
@endpush

