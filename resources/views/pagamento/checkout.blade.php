@extends('layouts.app')

@section('title', 'Checkout')

@push('styles')
<style>
    .plano-card {
        background: rgba(24, 24, 27, 0.8);
        border: 2px solid #3f3f46;
        border-radius: 12px;
        padding: 2rem;
    }
    input[type="radio"] {
        width: 20px;
        height: 20px;
        accent-color: #ef4444;
    }
    label:has(input[type="radio"]:checked) {
        border-color: #ef4444 !important;
        background: rgba(239, 68, 68, 0.1);
    }
</style>
@endpush

@section('content')
<div class="container mx-auto px-4 py-8 max-w-4xl">
    <h1 class="text-3xl font-black text-white mb-8">Finalizar Assinatura</h1>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
        <!-- Resumo do Plano -->
        <div>
            <div class="plano-card">
                <h2 class="text-xl font-bold text-white mb-4">Resumo</h2>
                <div class="mb-4">
                    <h3 class="text-lg font-bold text-white">{{ $plano->nome }}</h3>
                    <p class="text-neutral-400 text-sm mt-1">{{ $plano->descricao }}</p>
                </div>
                <div class="border-t border-neutral-700 pt-4">
                    <div class="flex justify-between items-center mb-2">
                        <span class="text-neutral-400">Valor</span>
                        <span class="text-xl font-bold text-white">R$ {{ number_format($plano->preco, 2, ',', '.') }}</span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-neutral-400">Periodicidade</span>
                        <span class="text-white">{{ $plano->periodicidade === 'monthly' ? 'Mensal' : 'Anual' }}</span>
                    </div>
                </div>
                @if($plano->features && count($plano->features) > 0)
                <div class="border-t border-neutral-700 pt-4 mt-4">
                    <h4 class="text-white font-bold mb-2">Inclui:</h4>
                    <ul class="space-y-2">
                        @foreach($plano->features as $feature)
                        <li class="flex items-center text-neutral-300 text-sm">
                            <i class="fas fa-check text-red-500 mr-2"></i>
                            {{ $feature }}
                        </li>
                        @endforeach
                    </ul>
                </div>
                @endif
            </div>
        </div>

        <!-- Informações -->
        <div>
            <div class="plano-card">
                <h2 class="text-xl font-bold text-white mb-4">Formas de Pagamento</h2>
                
                @if(!Auth::check())
                <div class="mb-6 p-4 bg-yellow-900/20 border border-yellow-700 rounded-lg">
                    <p class="text-yellow-400 text-sm mb-2">Você precisa estar logado para continuar.</p>
                    <div class="flex space-x-2">
                        <a href="{{ route('login') }}" class="btn-primary text-sm py-2 px-4">Fazer login</a>
                        <a href="{{ route('register') }}" class="btn-outline text-sm py-2 px-4">Criar conta</a>
                    </div>
                </div>
                @else
                <div class="space-y-4">
                    <div class="p-4 bg-green-900/20 border border-green-700 rounded-lg">
                        <p class="text-green-400 text-sm mb-2">
                            <i class="fas fa-shield-alt mr-2"></i>
                            Pagamento seguro processado pelo Asaas
                        </p>
                    </div>
                    
                    <form action="{{ route('pagamento.gerar-checkout', $plano->id) }}" method="POST" class="mt-6">
                        @csrf
                        
                        <div class="mb-6">
                            <label class="block text-sm font-medium text-white mb-3">Forma de Pagamento</label>
                            <div class="p-4 bg-neutral-900 border-2 border-red-500 rounded-lg">
                                <div class="flex items-center">
                                    <i class="fas fa-credit-card text-red-500 mr-3 text-xl"></i>
                                    <div class="flex-1">
                                        <span class="font-bold text-white">Cartão de Crédito</span>
                                        <p class="text-sm text-neutral-400 mt-1">Pagamento via checkout seguro do Asaas</p>
                                    </div>
                                </div>
                            </div>
                            <input type="hidden" name="billing_type" value="CREDIT_CARD">
                        </div>

                        <button type="submit" class="btn-primary w-full">
                            <i class="fas fa-lock mr-2"></i>
                            Continuar para Pagamento
                        </button>
                    </form>
                </div>
                @endif
            </div>

            <div class="plano-card mt-4">
                <h3 class="text-lg font-bold text-white mb-2">Informações Importantes</h3>
                <ul class="space-y-2 text-sm text-neutral-400">
                    <li><i class="fas fa-check-circle text-red-500 mr-2"></i> Acesso imediato após confirmação do pagamento</li>
                    <li><i class="fas fa-check-circle text-red-500 mr-2"></i> Renovação automática conforme periodicidade</li>
                    <li><i class="fas fa-check-circle text-red-500 mr-2"></i> Cancelamento a qualquer momento</li>
                </ul>
            </div>
        </div>
    </div>
</div>
@endsection