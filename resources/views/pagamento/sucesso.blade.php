@extends('layouts.app')

@section('title', 'Pagamento Processado')

@push('styles')
<style>
    .success-card {
        background: rgba(24, 24, 27, 0.8);
        border: 2px solid #10b981;
        border-radius: 12px;
        padding: 2rem;
    }
    .btn-outline {
        background: transparent;
        color: #ef4444;
        padding: 1rem 2rem;
        border-radius: 8px;
        font-weight: 700;
        border: 2px solid #ef4444;
        cursor: pointer;
        transition: all 0.3s;
        text-decoration: none;
        display: inline-block;
    }
    .btn-outline:hover {
        background: #ef4444;
        color: white;
    }
</style>
@endpush

@section('content')
<div class="container mx-auto px-4 py-8 max-w-2xl">
    <div class="success-card text-center">
        <div class="mb-6">
            <i class="fas fa-check-circle text-6xl text-green-500 mb-4"></i>
            <h1 class="text-3xl font-black text-white mb-2">Pagamento Recebido!</h1>
            <p class="text-neutral-400">Aguarde a confirmação para ativação completa da sua assinatura</p>
        </div>

        <div class="bg-neutral-900 rounded-lg p-6 mb-6 text-left">
            <h2 class="text-xl font-bold text-white mb-4">{{ $plano->nome }}</h2>
            <div class="space-y-2 text-neutral-300">
                <p><strong>Valor:</strong> R$ {{ number_format($plano->preco, 2, ',', '.') }}</p>
                <p><strong>Periodicidade:</strong> {{ $plano->periodicidade === 'monthly' ? 'Mensal' : 'Anual' }}</p>
                <p><strong>Status:</strong> 
                    <span class="px-3 py-1 rounded-full text-xs font-semibold {{ $assinatura->status === 'active' ? 'bg-green-900/50 text-green-400' : 'bg-yellow-900/50 text-yellow-400' }}">
                        {{ $assinatura->status === 'active' ? 'Ativo' : 'Aguardando Confirmação' }}
                    </span>
                </p>
            </div>
        </div>

        @if($assinatura->status === 'pending')
        <div class="bg-blue-900/20 border border-blue-700 rounded-lg p-4 mb-6">
            <p class="text-blue-400 text-sm">
                <i class="fas fa-info-circle mr-2"></i>
                Seu pagamento está sendo processado. Você receberá um email quando a assinatura for ativada.
            </p>
        </div>
        @endif

        <div class="space-y-3">
            <a href="{{ route('central-caverna') }}" class="btn-primary w-full inline-block text-center">
                <i class="fas fa-home mr-2"></i>
                Ir para o Dashboard
            </a>
            <a href="{{ route('settings') }}" class="btn-outline w-full inline-block text-center">
                <i class="fas fa-user mr-2"></i>
                Ver Minha Conta
            </a>
        </div>
    </div>
</div>
@endsection



