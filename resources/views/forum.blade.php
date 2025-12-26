@extends('layouts.app')

@section('title', 'Fórum')

@push('styles')
<style>
    .categoria-card {
        background: rgba(24, 24, 27, 0.8);
        border: 1px solid #3f3f46;
        border-radius: 12px;
        padding: 1.5rem;
        transition: all 0.3s ease;
        border-left: 4px solid;
    }
    .categoria-card:hover {
        transform: translateY(-2px);
        border-color: #ef4444;
        box-shadow: 0 4px 12px rgba(239, 68, 68, 0.2);
    }
    .btn-primary {
        background: #ef4444;
        color: white;
        padding: 0.75rem 1.5rem;
        border-radius: 8px;
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
    <div class="flex items-center justify-between mb-8">
        <div>
            <h1 class="text-4xl font-black text-white mb-2">Fórum da Comunidade</h1>
            <p class="text-neutral-400">Converse, compartilhe e aprenda com outros usuários</p>
        </div>
    </div>

    <!-- Feed Interativo de Posts -->
    @if(isset($feedPosts) && $feedPosts->count() > 0)
    <div class="mb-12">
        <div class="flex items-center justify-between mb-6">
            <h2 class="text-2xl font-bold text-white">Feed Recente</h2>
            <span class="text-sm text-neutral-400">Posts mais recentes de todas as categorias</span>
        </div>
        <div class="space-y-4">
            @foreach($feedPosts as $post)
            <a href="/forum/post/{{ $post->id }}" class="block">
                <div class="bg-neutral-800 rounded-lg p-6 border border-neutral-700 hover:border-red-500 transition-all duration-300 hover:shadow-lg hover:shadow-red-500/20">
                    <div class="flex items-start justify-between mb-3">
                        <div class="flex-1">
                            <div class="flex items-center space-x-3 mb-2">
                                @if($post->fixado)
                                <span class="text-red-500"><i class="fas fa-thumbtack"></i></span>
                                @endif
                                <span class="px-3 py-1 rounded-full text-xs font-semibold" style="background: {{ $post->categoria->cor }}20; color: {{ $post->categoria->cor }}">
                                    {{ $post->categoria->nome }}
                                </span>
                            </div>
                            <h3 class="text-xl font-bold text-white mb-2">{{ $post->titulo }}</h3>
                            <p class="text-neutral-400 line-clamp-2 mb-4">{{ Str::limit(strip_tags($post->conteudo), 200) }}</p>
                            <div class="flex items-center space-x-4 text-sm text-neutral-500">
                                <span class="flex items-center">
                                    <i class="fas fa-user mr-2"></i>{{ $post->user->name }}
                                </span>
                                <span class="flex items-center">
                                    <i class="fas fa-clock mr-2"></i>{{ $post->created_at->diffForHumans() }}
                                </span>
                                <span class="flex items-center">
                                    <i class="fas fa-comments mr-2"></i>{{ $post->todos_comentarios_count ?? 0 }} comentários
                                </span>
                                <span class="flex items-center">
                                    <i class="fas fa-eye mr-2"></i>{{ $post->visualizacoes }} visualizações
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </a>
            @endforeach
        </div>
    </div>
    @endif

    <div class="flex items-center justify-between mb-6">
        <h2 class="text-2xl font-bold text-white">Categorias</h2>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        @forelse($categorias as $categoria)
        <a href="/forum/categoria/{{ $categoria->id }}" class="categoria-card block" style="border-left-color: {{ $categoria->cor }}">
            <div class="flex items-start justify-between mb-4">
                <div class="flex items-center space-x-3">
                    <div class="w-12 h-12 rounded-lg flex items-center justify-center" style="background: {{ $categoria->cor }}20">
                        <i class="fas {{ $categoria->icone }} text-xl" style="color: {{ $categoria->cor }}"></i>
                    </div>
                    <div>
                        <h3 class="text-xl font-bold text-white">{{ $categoria->nome }}</h3>
                        <p class="text-sm text-neutral-400">{{ $categoria->posts_count }} posts</p>
                    </div>
                </div>
            </div>
            @if($categoria->descricao)
            <p class="text-sm text-neutral-300">{{ $categoria->descricao }}</p>
            @endif
        </a>
        @empty
        <div class="col-span-3 text-center py-12">
            <i class="fas fa-comments text-6xl text-neutral-600 mb-4"></i>
            <p class="text-neutral-400">Nenhuma categoria disponível ainda</p>
        </div>
        @endforelse
    </div>
</div>
@endsection
