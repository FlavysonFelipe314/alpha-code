@extends('layouts.app')

@section('title', $categoria->nome . ' - Fórum')

@push('styles')
<style>
    .post-card {
        background: rgba(24, 24, 27, 0.8);
        border: 1px solid #3f3f46;
        border-radius: 12px;
        padding: 1.5rem;
        transition: all 0.3s ease;
        border-left: 4px solid {{ $categoria->cor }};
    }
    .post-card:hover {
        transform: translateY(-2px);
        border-color: #ef4444;
    }
    .btn-primary {
        background: #ef4444;
        color: white;
        padding: 0.75rem 1.5rem;
        border-radius: 8px;
        font-weight: 700;
        border: none;
        cursor: pointer;
    }
    .modal {
        position: fixed; top: 0; left: 0; width: 100%; height: 100%;
        display: flex; align-items: center; justify-content: center;
        background-color: rgba(0, 0, 0, 0.8); backdrop-filter: blur(8px);
        z-index: 1000; opacity: 0; visibility: hidden; transition: opacity 0.3s;
    }
    .modal.active { opacity: 1; visibility: visible; }
    .modal-content {
        background: #18181b; border: 1px solid #3f3f46; border-radius: 12px;
        padding: 2rem; width: 90%; max-width: 600px; position: relative;
        max-height: 90vh; overflow-y: auto;
    }
    .close-btn { 
        position: absolute; top: 1rem; right: 1rem; background: none; border: none; 
        color: #71717a; font-size: 1.5rem; cursor: pointer; 
        width: 32px; height: 32px; display: flex; align-items: center; justify-content: center;
    }
    .close-btn:hover { color: white; }
    .form-input { 
        background: #27272a; border: 1px solid #3f3f46; border-radius: 8px; 
        padding: 0.75rem; color: white; width: 100%; 
        font-family: inherit;
    }
    .form-input:focus { outline: none; border-color: #ef4444; }
    label { color: #d4d4d8; }
</style>
@endpush

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="flex items-center justify-between mb-8">
        <div>
            <a href="/forum" class="text-red-500 hover:text-red-400 mb-2 inline-block">
                <i class="fas fa-arrow-left mr-2"></i>Voltar ao Fórum
            </a>
            <h1 class="text-4xl font-black text-white mb-2">{{ $categoria->nome }}</h1>
            @if($categoria->descricao)
            <p class="text-neutral-400">{{ $categoria->descricao }}</p>
            @endif
        </div>
        <button id="new-post-btn" class="btn-primary">
            <i class="fas fa-plus mr-2"></i>Novo Post
        </button>
    </div>

    <div id="posts-container" class="space-y-4">
        @forelse($categoria->posts as $post)
        <a href="/forum/post/{{ $post->id }}" class="post-card block">
            <div class="flex items-start justify-between">
                <div class="flex-1">
                    <div class="flex items-center space-x-2 mb-2">
                        @if($post->fixado)
                        <span class="text-red-500"><i class="fas fa-thumbtack"></i></span>
                        @endif
                        <h3 class="text-xl font-bold text-white">{{ $post->titulo }}</h3>
                    </div>
                    <p class="text-neutral-400 mb-3 line-clamp-2">{{ Str::limit(strip_tags($post->conteudo), 150) }}</p>
                    <div class="flex items-center space-x-4 text-sm text-neutral-500">
                        <span><i class="fas fa-user mr-1"></i>{{ $post->user->name }}</span>
                        <span><i class="fas fa-clock mr-1"></i>{{ $post->created_at->diffForHumans() }}</span>
                        <span><i class="fas fa-comments mr-1"></i>{{ $post->todos_comentarios_count ?? ($post->comentarios_count ?? 0) }} comentários</span>
                        <span><i class="fas fa-eye mr-1"></i>{{ $post->visualizacoes }} visualizações</span>
                    </div>
                </div>
            </div>
        </a>
        @empty
        <div class="text-center py-12">
            <i class="fas fa-comments text-6xl text-neutral-600 mb-4"></i>
            <p class="text-neutral-400 mb-4">Nenhum post nesta categoria ainda</p>
            <button id="new-post-btn-empty" class="btn-primary">Criar Primeiro Post</button>
        </div>
        @endforelse
    </div>
</div>

<!-- Modal Novo Post -->
<div id="new-post-modal" class="modal">
    <div class="modal-content max-w-2xl">
        <button class="close-btn" onclick="closeModal('new-post-modal')">&times;</button>
        <h2 class="text-2xl font-bold mb-4">Novo Post</h2>
        <form id="new-post-form">
            <input type="hidden" id="categoria-id" value="{{ $categoria->id }}">
            <div class="mb-4">
                <label class="block text-sm font-medium mb-2">Título</label>
                <input type="text" id="post-titulo" class="form-input" required>
            </div>
            <div class="mb-4">
                <label class="block text-sm font-medium mb-2">Conteúdo</label>
                <textarea id="post-conteudo" class="form-input" rows="10" required></textarea>
            </div>
            <button type="submit" class="btn-primary w-full">Publicar</button>
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
        const modal = document.getElementById(id);
        modal.classList.remove('active');
        // Limpa o formulário ao fechar
        if (id === 'new-post-modal') {
            document.getElementById('new-post-form').reset();
        }
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
    
    document.getElementById('new-post-btn')?.addEventListener('click', () => {
        document.getElementById('new-post-form').reset();
        openModal('new-post-modal');
    });
    document.getElementById('new-post-btn-empty')?.addEventListener('click', () => {
        document.getElementById('new-post-form').reset();
        openModal('new-post-modal');
    });
    
    // Fechar modal ao clicar fora
    document.getElementById('new-post-modal')?.addEventListener('click', (e) => {
        if (e.target.id === 'new-post-modal') {
            closeModal('new-post-modal');
        }
    });
    
    document.getElementById('new-post-form')?.addEventListener('submit', async (e) => {
        e.preventDefault();
        const data = {
            categoria_id: document.getElementById('categoria-id').value,
            titulo: document.getElementById('post-titulo').value,
            conteudo: document.getElementById('post-conteudo').value,
        };
        
        const response = await fetchAPI('/forum/posts', {
            method: 'POST',
            body: JSON.stringify(data)
        });
        
        if (response && response.status) {
            closeModal('new-post-modal');
            window.location.reload();
        } else {
            alert('Erro ao criar post: ' + (response?.message || 'Erro desconhecido'));
        }
    });
</script>
@endpush
