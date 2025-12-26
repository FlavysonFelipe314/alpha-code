@extends('layouts.app')

@section('title', 'Gerenciar Planos')

@push('styles')
<style>
    .form-input {
        background: #27272a;
        border: 1px solid #3f3f46;
        border-radius: 8px;
        padding: 0.75rem;
        color: white;
        width: 100%;
    }
    .form-input:focus {
        outline: none;
        border-color: #ef4444;
    }
</style>
@endpush

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="flex items-center justify-between mb-8">
        <h1 class="text-3xl font-black text-white">Gerenciar Planos</h1>
        <button id="new-plano-btn" class="btn-primary">
            <i class="fas fa-plus mr-2"></i>Novo Plano
        </button>
    </div>

    <div id="planos-container" class="space-y-4">
        <!-- Planos serão carregados aqui -->
    </div>
</div>

<!-- Modal Novo/Editar Plano -->
<div id="plano-modal" class="modal">
    <div class="modal-content max-w-2xl">
        <button class="close-btn" onclick="closeModal('plano-modal')">&times;</button>
        <h2 class="text-2xl font-bold mb-4" id="plano-modal-title">Novo Plano</h2>
        <form id="plano-form">
            <input type="hidden" id="plano-id">
            <div class="grid grid-cols-2 gap-4 mb-4">
                <div>
                    <label class="block text-sm font-medium mb-2">Nome</label>
                    <input type="text" id="plano-nome" class="form-input" required>
                </div>
                <div>
                    <label class="block text-sm font-medium mb-2">Preço</label>
                    <input type="number" step="0.01" id="plano-preco" class="form-input" required>
                </div>
            </div>
            <div class="mb-4">
                <label class="block text-sm font-medium mb-2">Descrição</label>
                <textarea id="plano-descricao" class="form-input" rows="3"></textarea>
            </div>
            <div class="grid grid-cols-2 gap-4 mb-4">
                <div>
                    <label class="block text-sm font-medium mb-2">Periodicidade</label>
                    <select id="plano-periodicidade" class="form-input" required>
                        <option value="monthly">Mensal</option>
                        <option value="yearly">Anual</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium mb-2">Ordem</label>
                    <input type="number" id="plano-ordem" class="form-input" value="0">
                </div>
            </div>
            <div class="mb-4">
                <label class="block text-sm font-medium mb-2">Features (uma por linha)</label>
                <textarea id="plano-features" class="form-input" rows="5" placeholder="Feature 1&#10;Feature 2"></textarea>
            </div>
            <div class="mb-4">
                <label class="flex items-center">
                    <input type="checkbox" id="plano-ativo" class="rounded border-gray-300 text-red-600">
                    <span class="ml-2 text-sm">Ativo</span>
                </label>
            </div>
            <button type="submit" class="btn-primary w-full">Salvar</button>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
    const API_BASE_URL = window.location.origin + '/api';
    
    function openModal(id) {
        document.getElementById(id).classList.add('active');
    }
    
    function closeModal(id) {
        document.getElementById(id).classList.remove('active');
        document.getElementById('plano-form').reset();
        document.getElementById('plano-id').value = '';
    }
    
    async function fetchAPI(endpoint, options = {}) {
        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
        const response = await fetch(`${API_BASE_URL}${endpoint}`, {
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': csrfToken || '',
                ...options.headers
            },
            credentials: 'include',
            ...options
        });
        return response.json();
    }
    
    async function loadPlanos() {
        const response = await fetchAPI('/admin/planos');
        if (response && response.status) {
            const container = document.getElementById('planos-container');
            container.innerHTML = '';
            
            response.data.forEach(plano => {
                const card = document.createElement('div');
                card.className = 'bg-neutral-800 rounded-lg p-6 border border-neutral-700';
                card.innerHTML = `
                    <div class="flex items-start justify-between">
                        <div class="flex-1">
                            <h3 class="text-2xl font-bold text-white mb-2">${plano.nome}</h3>
                            <p class="text-neutral-400 mb-4">${plano.descricao || ''}</p>
                            <div class="flex items-center space-x-4 text-sm">
                                <span class="text-red-500 font-bold">R$ ${parseFloat(plano.preco).toFixed(2)}</span>
                                <span class="text-neutral-400">/${plano.periodicidade === 'monthly' ? 'mês' : 'ano'}</span>
                                <span class="px-2 py-1 rounded ${plano.ativo ? 'bg-green-900/50 text-green-400' : 'bg-neutral-700 text-neutral-400'}">
                                    ${plano.ativo ? 'Ativo' : 'Inativo'}
                                </span>
                            </div>
                        </div>
                        <div class="flex space-x-2">
                            <button onclick="editPlano(${plano.id})" class="text-blue-400 hover:text-blue-300">
                                <i class="fas fa-edit"></i>
                            </button>
                            <button onclick="deletePlano(${plano.id})" class="text-red-400 hover:text-red-300">
                                <i class="fas fa-trash"></i>
                            </button>
                        </div>
                    </div>
                `;
                container.appendChild(card);
            });
        }
    }
    
    document.getElementById('new-plano-btn')?.addEventListener('click', () => {
        document.getElementById('plano-modal-title').textContent = 'Novo Plano';
        document.getElementById('plano-form').reset();
        document.getElementById('plano-id').value = '';
        document.getElementById('plano-ativo').checked = true;
        openModal('plano-modal');
    });
    
    document.getElementById('plano-form')?.addEventListener('submit', async (e) => {
        e.preventDefault();
        const id = document.getElementById('plano-id').value;
        const featuresText = document.getElementById('plano-features').value;
        const features = featuresText ? featuresText.split('\n').filter(f => f.trim()) : [];
        
        const data = {
            nome: document.getElementById('plano-nome').value,
            descricao: document.getElementById('plano-descricao').value,
            preco: parseFloat(document.getElementById('plano-preco').value),
            periodicidade: document.getElementById('plano-periodicidade').value,
            ordem: parseInt(document.getElementById('plano-ordem').value) || 0,
            features: features,
            ativo: document.getElementById('plano-ativo').checked,
            moeda: 'BRL',
        };
        
        const endpoint = id ? `/admin/planos/${id}` : '/admin/planos';
        const method = id ? 'PUT' : 'POST';
        
        const response = await fetchAPI(endpoint, {
            method,
            body: JSON.stringify(data)
        });
        
        if (response && response.status) {
            closeModal('plano-modal');
            loadPlanos();
        } else {
            alert('Erro ao salvar plano');
        }
    });
    
    async function editPlano(id) {
        const response = await fetchAPI(`/admin/planos/${id}`);
        if (response && response.status) {
            const plano = response.data;
            document.getElementById('plano-modal-title').textContent = 'Editar Plano';
            document.getElementById('plano-id').value = plano.id;
            document.getElementById('plano-nome').value = plano.nome;
            document.getElementById('plano-descricao').value = plano.descricao || '';
            document.getElementById('plano-preco').value = plano.preco;
            document.getElementById('plano-periodicidade').value = plano.periodicidade;
            document.getElementById('plano-ordem').value = plano.ordem || 0;
            document.getElementById('plano-features').value = plano.features ? plano.features.join('\n') : '';
            document.getElementById('plano-ativo').checked = plano.ativo;
            openModal('plano-modal');
        }
    }
    
    async function deletePlano(id) {
        if (!confirm('Tem certeza que deseja deletar este plano?')) return;
        const response = await fetchAPI(`/admin/planos/${id}`, { method: 'DELETE' });
        if (response && response.status) {
            loadPlanos();
        } else {
            alert(response?.message || 'Erro ao deletar plano');
        }
    }
    
    document.addEventListener('DOMContentLoaded', loadPlanos);
</script>
@endpush



