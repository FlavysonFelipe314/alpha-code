<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>AlphaCode - Modo Caverna</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;700;900&display=swap" rel="stylesheet">
    <style>
        body { 
            font-family: 'Inter', sans-serif; 
            background: #0A0A0A;
            color: #E0E0E0;
        }
        .hero-gradient {
            background: radial-gradient(circle at 50% 50%, rgba(239, 68, 68, 0.2) 0%, rgba(10,10,10,0) 70%);
        }
        .plan-card {
            background: rgba(24, 24, 27, 0.8);
            border: 2px solid #3f3f46;
            border-radius: 16px;
            padding: 2rem;
            transition: all 0.3s ease;
        }
        .plan-card:hover {
            border-color: #ef4444;
            transform: translateY(-8px);
            box-shadow: 0 20px 40px rgba(239, 68, 68, 0.2);
        }
        .plan-card.featured {
            border-color: #ef4444;
            background: linear-gradient(135deg, rgba(239, 68, 68, 0.1) 0%, rgba(24, 24, 27, 0.9) 100%);
        }
        .btn-primary {
            background: #ef4444;
            color: white;
            padding: 1rem 2rem;
            border-radius: 8px;
            font-weight: 700;
            border: none;
            cursor: pointer;
            transition: all 0.3s;
        }
        .btn-primary:hover {
            background: #dc2626;
            transform: scale(1.05);
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
        }
        .btn-outline:hover {
            background: #ef4444;
            color: white;
        }
    </style>
</head>
<body>
    <!-- Hero Section -->
    <div class="hero-gradient min-h-screen flex flex-col">
        <!-- Navbar -->
        <nav class="container mx-auto px-4 py-6 flex justify-between items-center">
            <div class="flex items-center space-x-3">
                <img src="{{ asset('Assets/logo.png') }}" alt="Logo" class="h-12 w-auto">
                <span class="text-xl font-black text-white">ALPHACODE</span>
            </div>
            <div class="flex items-center space-x-4">
                <a href="{{ route('login') }}" class="text-neutral-400 hover:text-white transition">Login</a>
                <a href="{{ route('register') }}" class="btn-outline">Registrar</a>
            </div>
        </nav>

        <!-- Hero Content -->
        <div class="container mx-auto px-4 flex-1 flex items-center">
            <div class="max-w-4xl mx-auto text-center">
                <h1 class="text-6xl font-black text-white mb-6 leading-tight">
                    Transforme sua vida com
                    <span class="text-red-500">Modo Caverna</span>
                </h1>
                <p class="text-xl text-neutral-400 mb-12 max-w-2xl mx-auto">
                    A plataforma completa para gerenciar sua produtividade, treinos, dieta, finanças e muito mais. 
                    Tudo em um só lugar, projetado para quem busca excelência.
                </p>
                <div class="flex justify-center space-x-4 mb-16">
                    <a href="#planos" class="btn-primary">Começar Agora</a>
                    <a href="{{ route('login') }}" class="btn-outline">Já tenho conta</a>
                </div>

                <!-- Features Preview -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-8 text-left">
                    <div class="bg-neutral-900/50 rounded-lg p-6">
                        <i class="fas fa-tasks text-red-500 text-3xl mb-4"></i>
                        <h3 class="text-xl font-bold text-white mb-2">Produtividade</h3>
                        <p class="text-neutral-400">Pomodoro, Kanban, tarefas e metas organizadas</p>
                    </div>
                    <div class="bg-neutral-900/50 rounded-lg p-6">
                        <i class="fas fa-dumbbell text-red-500 text-3xl mb-4"></i>
                        <h3 class="text-xl font-bold text-white mb-2">Treino & Dieta</h3>
                        <p class="text-neutral-400">Planos personalizados com IA e acompanhamento</p>
                    </div>
                    <div class="bg-neutral-900/50 rounded-lg p-6">
                        <i class="fas fa-chart-line text-red-500 text-3xl mb-4"></i>
                        <h3 class="text-xl font-bold text-white mb-2">Financeiro</h3>
                        <p class="text-neutral-400">Controle completo das suas finanças</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Planos Section -->
    <section id="planos" class="py-20 bg-neutral-900">
        <div class="container mx-auto px-4">
            <div class="text-center mb-16">
                <h2 class="text-5xl font-black text-white mb-4">Escolha seu Plano</h2>
                <p class="text-xl text-neutral-400">Planos pensados para diferentes necessidades</p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-8 max-w-6xl mx-auto">
                @forelse($planos as $index => $plano)
                <div class="plan-card {{ $index === 1 ? 'featured' : '' }}">
                    @if($index === 1)
                    <div class="text-center mb-4">
                        <span class="bg-red-500 text-white text-xs font-bold px-3 py-1 rounded-full">POPULAR</span>
                    </div>
                    @endif
                    <h3 class="text-2xl font-black text-white mb-2">{{ $plano->nome }}</h3>
                    <div class="mb-6">
                        <span class="text-4xl font-black text-white">R$ {{ number_format($plano->preco, 2, ',', '.') }}</span>
                        <span class="text-neutral-400">/{{ $plano->periodicidade === 'monthly' ? 'mês' : 'ano' }}</span>
                    </div>
                    @if($plano->descricao)
                    <p class="text-neutral-400 mb-6">{{ $plano->descricao }}</p>
                    @endif
                    @if($plano->features && count($plano->features) > 0)
                    <ul class="space-y-3 mb-8">
                        @foreach($plano->features as $feature)
                        <li class="flex items-center text-neutral-300">
                            <i class="fas fa-check text-red-500 mr-3"></i>
                            {{ $feature }}
                        </li>
                        @endforeach
                    </ul>
                    @endif
                    <a href="{{ route('register') }}" class="btn-primary w-full inline-block text-center">
                        {{ $index === 1 ? 'Começar Agora' : 'Registrar' }}
                    </a>
                </div>
                @empty
                <div class="col-span-3 text-center py-12">
                    <p class="text-neutral-400">Nenhum plano disponível no momento.</p>
                </div>
                @endforelse
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="bg-black py-12">
        <div class="container mx-auto px-4 text-center">
            <img src="{{ asset('Assets/logo.png') }}" alt="Logo" class="h-16 w-auto mx-auto mb-4">
            <p class="text-neutral-400 mb-4">AlphaCode - Modo Caverna</p>
            <p class="text-neutral-500 text-sm">© 2025 Todos os direitos reservados</p>
        </div>
    </footer>
</body>
</html>
