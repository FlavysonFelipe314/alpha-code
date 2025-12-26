<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Modo Caverna') - AlphaCode</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;700;900&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary-color: {{ auth()->user()->theme_colors['primary'] ?? '#ef4444' }};
            --secondary-color: {{ auth()->user()->theme_colors['secondary'] ?? '#dc2626' }};
        }
        body { 
            font-family: 'Inter', sans-serif; 
            background-color: #0A0A0A; 
            color: #E0E0E0; 
            margin: 0;
            padding: 0;
        }
        .immersive-background {
            position: fixed; 
            top: 0; 
            left: 0; 
            width: 100%; 
            height: 100%;
            background: radial-gradient(circle at 50% 50%, rgba(176, 26, 26, 0.15) 0%, rgba(10,10,10,0) 60%);
            animation: pulse-background 10s infinite ease-in-out;
            z-index: -1;
        }
        @keyframes pulse-background {
            0%, 100% { transform: scale(1); opacity: 0.8; }
            50% { transform: scale(1.2); opacity: 1; }
        }
        ::-webkit-scrollbar { width: 8px; height: 8px; }
        ::-webkit-scrollbar-track { background: #18181b; }
        ::-webkit-scrollbar-thumb { background: var(--primary-color); border-radius: 4px; }
        ::-webkit-scrollbar-thumb:hover { background: var(--secondary-color); }

        /* Sidebar Styles */
        .sidebar {
            position: fixed;
            left: 0;
            top: 0;
            height: 100vh;
            width: 80px;
            background: rgba(24, 24, 27, 0.95);
            backdrop-filter: blur(10px);
            border-right: 1px solid #3f3f46;
            display: flex;
            flex-direction: column;
            align-items: center;
            padding: 1rem 0;
            z-index: 100;
            transition: width 0.3s ease;
        }

        .sidebar:hover {
            width: 240px;
        }

        .sidebar-logo {
            width: 130px;
            height: 130px;
            background: transparent;
            border-radius: 0;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 2rem;
            transition: transform 0.3s ease;
            padding: 0;
        }
        
        .sidebar-logo img {
            width: 100%;
            height: 100%;
            object-fit: contain;
        }

        .sidebar:hover .sidebar-logo {
            transform: scale(1.1);
        }

        .sidebar-nav {
            flex: 1;
            width: 100%;
            display: flex;
            flex-direction: column;
            gap: 0.5rem;
            padding: 0 0.75rem;
            overflow-y: auto;
            overflow-x: hidden;
            min-height: 0;
        }
        
        .sidebar-nav::-webkit-scrollbar {
            width: 4px;
        }
        
        .sidebar-nav::-webkit-scrollbar-track {
            background: transparent;
        }
        
        .sidebar-nav::-webkit-scrollbar-thumb {
            background: rgba(239, 68, 68, 0.3);
            border-radius: 2px;
        }
        
        .sidebar-nav::-webkit-scrollbar-thumb:hover {
            background: rgba(239, 68, 68, 0.5);
        }
        
        .sidebar-bottom {
            width: 100%;
            display: flex;
            flex-direction: column;
            gap: 0.5rem;
            padding: 0 0.75rem;
            margin-top: auto;
            margin-bottom: 1rem;
        }

        .nav-item {
            position: relative;
            width: 100%;
            display: flex;
            align-items: center;
            padding: 0.75rem;
            border-radius: 12px;
            color: #a1a1aa;
            text-decoration: none;
            transition: all 0.3s ease;
            overflow: hidden;
            white-space: nowrap;
        }

        .nav-item:hover {
            background: rgba(239, 68, 68, 0.1);
            color: #fff;
        }
        
        button.nav-item {
            background: transparent;
            border: none;
            width: 100%;
            display: flex;
            align-items: center;
            padding: 0.75rem;
            cursor: pointer;
            position: relative;
            border-radius: 12px;
            color: #a1a1aa;
            text-decoration: none;
            transition: all 0.3s ease;
            overflow: hidden;
            white-space: nowrap;
            margin: 0;
            font-family: inherit;
            font-size: inherit;
        }
        
        button.nav-item:hover {
            background: rgba(239, 68, 68, 0.1);
            color: #fff;
        }

        .nav-item.active {
            background: rgba(239, 68, 68, 0.2);
            color: var(--primary-color);
        }

        .nav-item.active::before {
            content: '';
            position: absolute;
            left: 0;
            top: 0;
            bottom: 0;
            width: 3px;
            background: var(--primary-color);
        }

        .nav-item-icon {
            width: 24px;
            height: 24px;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
            font-size: 1.25rem;
        }

        .nav-item-label {
            margin-left: 1rem;
            font-weight: 500;
            font-size: 0.875rem;
            opacity: 0;
            transition: opacity 0.3s ease;
        }

        .sidebar:hover .nav-item-label {
            opacity: 1;
        }

        .main-content {
            margin-left: 80px;
            min-height: 100vh;
            transition: margin-left 0.3s ease;
        }

        .sidebar:hover ~ .main-content {
            margin-left: 240px;
        }

        @media (max-width: 768px) {
            .sidebar {
                width: 70px;
            }
            .sidebar:hover {
                width: 70px;
            }
            .main-content {
                margin-left: 70px;
            }
            .sidebar:hover ~ .main-content {
                margin-left: 70px;
            }
            .nav-item-label {
                display: none;
            }
        }

        /* Card Styles */
        .card {
            background: rgba(24, 24, 27, 0.8);
            border: 1px solid #3f3f46;
            border-radius: 12px;
            padding: 1.5rem;
        }

        /* Button Styles */
        .btn-primary {
            background: var(--primary-color);
            color: white;
            padding: 0.75rem 1.5rem;
            border-radius: 8px;
            font-weight: 700;
            transition: background 0.3s;
            border: none;
            cursor: pointer;
        }
        .btn-primary:hover {
            background: var(--secondary-color);
        }
    </style>
    @stack('styles')
</head>
<body>
    <div class="immersive-background"></div>

    <!-- Sidebar -->
    <aside class="sidebar">
        <div class="sidebar-logo">
            <img src="{{ asset('Assets/logo.png') }}" alt="Logo" class="w-full h-full object-contain">
        </div>
        
        <nav class="sidebar-nav">
            <a href="/central-caverna" class="nav-item {{ request()->is('central-caverna') || request()->is('/') ? 'active' : '' }}" title="Início">
                <div class="nav-item-icon">
                    <i class="fas fa-home"></i>
                </div>
                <span class="nav-item-label">Início</span>
            </a>

            <a href="/foco" class="nav-item {{ request()->is('foco') ? 'active' : '' }}" title="Foco">
                <div class="nav-item-icon">
                    <i class="fas fa-tasks"></i>
                </div>
                <span class="nav-item-label">Foco</span>
            </a>

            <a href="/treino" class="nav-item {{ request()->is('treino') ? 'active' : '' }}" title="Treino">
                <div class="nav-item-icon">
                    <i class="fas fa-dumbbell"></i>
                </div>
                <span class="nav-item-label">Treino</span>
            </a>

            <a href="/agenda" class="nav-item {{ request()->is('agenda') ? 'active' : '' }}" title="Agenda">
                <div class="nav-item-icon">
                    <i class="fas fa-calendar"></i>
                </div>
                <span class="nav-item-label">Agenda</span>
            </a>

            <a href="/dieta" class="nav-item {{ request()->is('dieta') ? 'active' : '' }}" title="Dieta">
                <div class="nav-item-icon">
                    <i class="fas fa-utensils"></i>
                </div>
                <span class="nav-item-label">Dieta</span>
            </a>

            <a href="/financa" class="nav-item {{ request()->is('financa') ? 'active' : '' }}" title="Finanças">
                <div class="nav-item-icon">
                    <i class="fas fa-wallet"></i>
                </div>
                <span class="nav-item-label">Finanças</span>
            </a>

            <a href="/biblioteca" class="nav-item {{ request()->is('biblioteca') ? 'active' : '' }}" title="Biblioteca">
                <div class="nav-item-icon">
                    <i class="fas fa-book"></i>
                </div>
                <span class="nav-item-label">Biblioteca</span>
            </a>

            <a href="/objetivo" class="nav-item {{ request()->is('objetivo') ? 'active' : '' }}" title="Objetivos">
                <div class="nav-item-icon">
                    <i class="fas fa-bullseye"></i>
                </div>
                <span class="nav-item-label">Objetivos</span>
            </a>

            <a href="/anotacoes" class="nav-item {{ request()->is('anotacoes') ? 'active' : '' }}" title="Anotações">
                <div class="nav-item-icon">
                    <i class="fas fa-sticky-note"></i>
                </div>
                <span class="nav-item-label">Anotações</span>
            </a>

            <a href="/forum" class="nav-item {{ request()->is('forum*') ? 'active' : '' }}" title="Fórum">
                <div class="nav-item-icon">
                    <i class="fas fa-comments"></i>
                </div>
                <span class="nav-item-label">Fórum</span>
            </a>
        </nav>

        <div class="sidebar-bottom">
            @auth
            @if(auth()->user()->is_admin)
            <a href="/admin/users" class="nav-item {{ request()->is('admin/users*') ? 'active' : '' }}" title="Usuários">
                <div class="nav-item-icon">
                    <i class="fas fa-users-cog"></i>
                </div>
                <span class="nav-item-label">Usuários</span>
            </a>
            <a href="/admin/planos" class="nav-item {{ request()->is('admin/planos*') ? 'active' : '' }}" title="Planos">
                <div class="nav-item-icon">
                    <i class="fas fa-credit-card"></i>
                </div>
                <span class="nav-item-label">Planos</span>
            </a>
            @endif
            
            <a href="/settings" class="nav-item {{ request()->is('settings') ? 'active' : '' }}" title="Perfil e Configurações">
                <div class="nav-item-icon">
                    <i class="fas fa-user"></i>
                </div>
                <span class="nav-item-label">Perfil</span>
            </a>

            <form id="logout-form" action="{{ route('logout') }}" method="POST" class="w-full">
                @csrf
                <button type="submit" class="nav-item" title="Sair">
                    <div class="nav-item-icon">
                        <i class="fas fa-sign-out-alt"></i>
                    </div>
                    <span class="nav-item-label">Sair</span>
                </button>
            </form>
            @endauth
        </div>
    </aside>

    <!-- Main Content -->
    <main class="main-content">
        @yield('content')
    </main>

    @stack('scripts')
</body>
</html>

