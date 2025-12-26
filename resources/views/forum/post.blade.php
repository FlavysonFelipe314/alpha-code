@extends('layouts.app')

@section('title', $post->titulo . ' - Fórum')

@push('styles')
<style>
    .comment-card {
        background: rgba(24, 24, 27, 0.6);
        border: 1px solid #3f3f46;
        border-radius: 12px;
        padding: 1.5rem;
        margin-bottom: 1rem;
    }
    .comment-reply {
        margin-left: 3rem;
        border-left: 3px solid #ef4444;
        padding-left: 1rem;
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
</style>
@endpush

@section('content')
<div class="container mx-auto px-4 py-8 max-w-4xl">
    <a href="/forum/categoria/{{ $post->categoria_id }}" class="text-red-500 hover:text-red-400 mb-4 inline-block">
        <i class="fas fa-arrow-left mr-2"></i>Voltar para {{ $post->categoria->nome }}
    </a>

    <div class="bg-neutral-800 rounded-lg p-6 mb-6">
        <div class="flex items-start justify-between mb-4">
            <div class="flex-1">
                <div class="flex items-center space-x-2 mb-2">
                    @if($post->fixado)
                    <span class="text-red-500"><i class="fas fa-thumbtack"></i></span>
                    @endif
                    <h1 class="text-3xl font-black text-white">{{ $post->titulo }}</h1>
                </div>
                <div class="flex items-center space-x-4 text-sm text-neutral-400 mb-4">
                    <span><i class="fas fa-user mr-1"></i>{{ $post->user->name }}</span>
                    <span><i class="fas fa-clock mr-1"></i>{{ $post->created_at->diffForHumans() }}</span>
                    <span><i class="fas fa-eye mr-1"></i>{{ $post->visualizacoes }} visualizações</span>
                </div>
            </div>
        </div>
        <div class="prose prose-invert max-w-none">
            <p class="text-neutral-300 whitespace-pre-wrap">{{ $post->conteudo }}</p>
        </div>
    </div>

    <div class="mb-6">
        <h2 class="text-2xl font-bold text-white mb-4">Comentários ({{ $post->comentarios->count() }})</h2>
        <button id="new-comment-btn" class="btn-primary mb-4">
            <i class="fas fa-plus mr-2"></i>Novo Comentário
        </button>
        
        <div id="comments-container" class="space-y-4">
            @forelse($post->comentarios as $comentario)
            <div class="comment-card">
                <div class="flex items-start justify-between mb-2">
                    <div>
                        <span class="font-bold text-white">{{ $comentario->user->name }}</span>
                        <span class="text-sm text-neutral-400 ml-2">{{ $comentario->created_at->diffForHumans() }}</span>
                    </div>
                </div>
                <p class="text-neutral-300 whitespace-pre-wrap mb-3">{{ $comentario->conteudo }}</p>
                <button onclick="replyTo({{ $comentario->id }}, '{{ $comentario->user->name }}')" class="text-sm text-red-500 hover:text-red-400">
                    <i class="fas fa-reply mr-1"></i>Responder
                </button>
                
                @if($comentario->respostas->count() > 0)
                <div class="mt-4">
                    @foreach($comentario->respostas as $resposta)
                    <div class="comment-card comment-reply">
                        <div class="flex items-start justify-between mb-2">
                            <div>
                                <span class="font-bold text-white">{{ $resposta->user->name }}</span>
                                <span class="text-sm text-neutral-400 ml-2">{{ $resposta->created_at->diffForHumans() }}</span>
                            </div>
                        </div>
                        <p class="text-neutral-300 whitespace-pre-wrap">{{ $resposta->conteudo }}</p>
                    </div>
                    @endforeach
                </div>
                @endif
            </div>
            @empty
            <p class="text-neutral-400 text-center py-8">Nenhum comentário ainda. Seja o primeiro!</p>
            @endforelse
        </div>
    </div>
</div>

<!-- Modal Novo Comentário -->
<div id="new-comment-modal" class="modal">
    <div class="modal-content max-w-2xl">
        <button class="close-btn" onclick="closeModal('new-comment-modal')">&times;</button>
        <h2 class="text-2xl font-bold mb-4" id="comment-modal-title">Novo Comentário</h2>
        <form id="new-comment-form">
            <input type="hidden" id="post-id" value="{{ $post->id }}">
            <input type="hidden" id="parent-comment-id">
            <div class="mb-4">
                <label class="block text-sm font-medium mb-2">Comentário</label>
                <textarea id="comment-conteudo" class="form-input" rows="6" required></textarea>
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
        document.getElementById(id).classList.remove('active');
        document.getElementById('parent-comment-id').value = '';
        document.getElementById('comment-conteudo').value = '';
        document.getElementById('comment-modal-title').textContent = 'Novo Comentário';
    }
    
    function replyTo(commentId, userName) {
        document.getElementById('parent-comment-id').value = commentId;
        document.getElementById('comment-modal-title').textContent = 'Responder para ' + userName;
        openModal('new-comment-modal');
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
    
    document.getElementById('new-comment-btn')?.addEventListener('click', () => {
        closeModal('new-comment-modal');
        openModal('new-comment-modal');
    });
    
    document.getElementById('new-comment-form')?.addEventListener('submit', async (e) => {
        e.preventDefault();
        const data = {
            post_id: document.getElementById('post-id').value,
            comentario_pai_id: document.getElementById('parent-comment-id').value || null,
            conteudo: document.getElementById('comment-conteudo').value,
        };
        
        const response = await fetchAPI('/forum/comentarios', {
            method: 'POST',
            body: JSON.stringify(data)
        });
        
        if (response.status) {
            window.location.reload();
        } else {
            alert('Erro ao criar comentário: ' + (response.message || 'Erro desconhecido'));
        }
    });
</script>
@endpush



