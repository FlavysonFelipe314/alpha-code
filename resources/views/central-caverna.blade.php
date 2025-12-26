@extends('layouts.app')

@section('title', 'Início')

@push('styles')
<style>
    .card { 
        background: rgba(24, 24, 27, 0.8); 
        border: 1px solid #3f3f46; 
        border-radius: 12px; 
        padding: 1.5rem;
        transition: transform 0.2s, border-color 0.2s;
    }
    .card:hover {
        border-color: #ef4444;
        transform: translateY(-2px);
    }
    .stat-card {
        background: linear-gradient(135deg, rgba(239, 68, 68, 0.1) 0%, rgba(24, 24, 27, 0.8) 100%);
        border-left: 4px solid #ef4444;
    }
    .stat-number {
        font-size: 2.5rem;
        font-weight: 900;
        background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
    }
    .btn-secondary { 
        background: #ef4444; 
        color: white; 
        padding: 0.75rem 1.5rem; 
        border-radius: 8px; 
        font-weight: 700; 
        transition: background 0.3s; 
        border: none; 
        cursor: pointer; 
    }
    .btn-secondary:hover { background: #dc2626; }
    .pill { 
        display: inline-block; 
        padding: 0.5rem 1rem; 
        border-radius: 9999px; 
        font-size: 0.875rem; 
        font-weight: 600; 
    }
    .pill-red { background: #ef4444; color: white; }
    .tab { padding: 0.5rem 1rem; border-bottom: 2px solid transparent; cursor: pointer; color: #a1a1aa; }
    .tab.active { border-bottom-color: #ef4444; color: #ef4444; }
    .modal {
        position: fixed; top: 0; left: 0; width: 100%; height: 100%;
        display: flex; align-items: center; justify-content: center;
        background-color: rgba(0, 0, 0, 0.8); backdrop-filter: blur(8px);
        z-index: 1000; opacity: 0; visibility: hidden; transition: opacity 0.3s;
    }
    .modal.active { opacity: 1; visibility: visible; }
    .modal-content {
        background: #18181b; border: 1px solid #3f3f46; border-radius: 12px;
        padding: 2rem; width: 90%; max-width: 500px; position: relative;
    }
    .close-btn { position: absolute; top: 1rem; right: 1rem; background: none; border: none; color: #71717a; font-size: 1.5rem; cursor: pointer; }
    .form-input { background: #27272a; border: 1px solid #3f3f46; border-radius: 8px; padding: 0.75rem; color: white; width: 100%; }
    .form-input:focus { outline: none; border-color: #ef4444; }
    .progress-bar {
        height: 8px;
        background: #3f3f46;
        border-radius: 4px;
        overflow: hidden;
    }
    .progress-fill {
        height: 100%;
        background: linear-gradient(90deg, #ef4444 0%, #dc2626 100%);
        transition: width 0.5s ease;
    }
    .bar-chart-bar {
        background: linear-gradient(to top, #ef4444 0%, #dc2626 100%);
        border-radius: 4px 4px 0 0;
        transition: height 0.5s ease;
    }
</style>
@endpush

@section('content')
<div class="container mx-auto px-4 py-8">
    <!-- Header com saudação -->
    <div class="mb-8">
        <h1 class="text-4xl font-black text-white mb-2">
            Olá, {{ auth()->user()->name }}! 👋
        </h1>
        <p class="text-neutral-400">Aqui está o seu panorama geral de hoje</p>
    </div>

    <!-- Cards de Estatísticas -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <!-- Tarefas -->
        <div class="card stat-card">
            <div class="flex items-center justify-between mb-2">
                <span class="text-neutral-400 text-sm">Tarefas</span>
                <i class="fas fa-tasks text-red-500"></i>
            </div>
            <div class="stat-number">{{ $stats['tarefas']['total'] }}</div>
            <div class="mt-2 space-y-1">
                <p class="text-sm text-neutral-400">{{ $stats['tarefas']['pendentes'] }} total</p>
                @if($stats['tarefas']['hoje'] > 0)
                <p class="text-xs text-red-400 font-semibold">+{{ $stats['tarefas']['hoje'] }} hoje</p>
                @endif
                @if($stats['tarefas']['semana'] > 0)
                <p class="text-xs text-neutral-500">{{ $stats['tarefas']['semana'] }} esta semana</p>
                @endif
            </div>
        </div>

        <!-- Objetivos -->
        <div class="card stat-card">
            <div class="flex items-center justify-between mb-2">
                <span class="text-neutral-400 text-sm">Objetivos</span>
                <i class="fas fa-bullseye text-red-500"></i>
            </div>
            <div class="stat-number">{{ $stats['objetivos']['total'] }}</div>
            <div class="mt-2">
                <div class="progress-bar">
                    <div class="progress-fill" style="width: {{ $stats['objetivos']['total'] > 0 ? ($stats['objetivos']['completados'] / $stats['objetivos']['total'] * 100) : 0 }}%"></div>
                </div>
                <p class="text-xs text-neutral-400 mt-1">{{ $stats['objetivos']['completados'] }} de {{ $stats['objetivos']['total'] }} completados</p>
                @if($stats['objetivos']['pendentes'] > 0)
                <p class="text-xs text-red-400 mt-1 font-semibold">{{ $stats['objetivos']['pendentes'] }} pendentes</p>
                @endif
            </div>
        </div>

        <!-- Pomodoros da Semana -->
        <div class="card stat-card">
            <div class="flex items-center justify-between mb-2">
                <span class="text-neutral-400 text-sm">Foco</span>
                <i class="fas fa-clock text-red-500"></i>
            </div>
            <div class="stat-number">{{ $stats['pomodoros']['semana'] }}</div>
            <div class="mt-2 space-y-1">
                <p class="text-sm text-neutral-400">{{ round($stats['pomodoros']['total_minutos'] / 60, 1) }}h esta semana</p>
                @if($stats['pomodoros']['hoje'] > 0)
                <p class="text-xs text-red-400 font-semibold">{{ $stats['pomodoros']['hoje'] }} hoje ({{ round($stats['pomodoros']['hoje_minutos'] / 60, 1) }}h)</p>
                @endif
                @if($stats['pomodoros']['total_geral'] > 0)
                <p class="text-xs text-neutral-500">{{ $stats['pomodoros']['total_geral'] }} total</p>
                @endif
            </div>
        </div>

        <!-- Treinos da Semana -->
        <div class="card stat-card">
            <div class="flex items-center justify-between mb-2">
                <span class="text-neutral-400 text-sm">Treinos</span>
                <i class="fas fa-dumbbell text-red-500"></i>
            </div>
            <div class="stat-number">{{ $stats['treinos']['semana'] }}</div>
            <div class="mt-2">
                <div class="progress-bar">
                    <div class="progress-fill" style="width: {{ $stats['treinos']['semana'] > 0 ? ($stats['treinos']['realizados'] / $stats['treinos']['semana'] * 100) : 0 }}%"></div>
                </div>
                <p class="text-xs text-neutral-400 mt-1">{{ $stats['treinos']['realizados'] }} realizados</p>
                @if($stats['treinos']['hoje'] > 0)
                <p class="text-xs text-red-400 mt-1 font-semibold">{{ $stats['treinos']['hoje'] }} hoje</p>
                @endif
                @if($stats['treinos']['total_mes'] > 0)
                <p class="text-xs text-neutral-500 mt-1">{{ $stats['treinos']['total_mes'] }} este mês</p>
                @endif
            </div>
        </div>
    </div>

    <!-- Grid Principal -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">
        
        <!-- Card: Produtividade Semanal -->
        <div class="card lg:col-span-2">
            <div class="flex items-center justify-between mb-6">
                <h3 class="text-xl font-bold text-white">Produtividade Semanal</h3>
                <span class="pill pill-red">FLOW</span>
            </div>
            <div class="flex justify-between items-end" style="height: 200px;">
                @foreach($produtividade as $day)
                <div class="flex flex-col items-center flex-1">
                    <div class="bar-chart-bar w-full mb-2" style="height: {{ max(10, $day['altura']) }}%;"></div>
                    <span class="text-xs text-neutral-500 font-medium">{{ $day['dia_nome'] }}</span>
                    <span class="text-xs text-neutral-600 mt-1">{{ round($day['minutos'] / 60, 1) }}h</span>
                </div>
                @endforeach
            </div>
        </div>

        <!-- Card: Próximos Compromissos -->
        <div class="card">
            <div class="flex items-center justify-between mb-4">
                <div>
                    <h3 class="text-lg font-bold text-white">Agenda</h3>
                    <p class="text-xs text-neutral-400">
                        @if($stats['agenda']['hoje'] > 0)
                            {{ $stats['agenda']['hoje'] }} hoje
                        @else
                            Nenhum hoje
                        @endif
                        @if($stats['agenda']['completados_hoje'] > 0)
                            • {{ $stats['agenda']['completados_hoje'] }} concluídos
                        @endif
                    </p>
                </div>
                <span class="w-2 h-2 bg-red-500 rounded-full animate-pulse"></span>
            </div>
            <div id="agenda-content" class="space-y-3">
                @if(count($stats['agenda']['proximos']) > 0)
                    @foreach($stats['agenda']['proximos']->take(3) as $evento)
                    <div class="bg-neutral-800 rounded-lg p-3 border-l-4 border-red-500">
                        <div class="flex items-start justify-between">
                            <div class="flex-1">
                                <h4 class="font-bold text-white text-sm mb-1">{{ $evento->title }}</h4>
                                <p class="text-xs text-red-400 font-semibold">
                                    {{ \Carbon\Carbon::parse($evento->date)->format('d/m') }} às {{ substr($evento->time ?? '00:00', 0, 5) }}
                                </p>
                            </div>
                        </div>
                    </div>
                    @endforeach
                @else
                    <div class="text-center py-8">
                        <i class="fas fa-calendar-times text-4xl text-neutral-600 mb-3"></i>
                        <p class="text-sm text-neutral-500">Nenhum compromisso</p>
                    </div>
                @endif
            </div>
            <button id="add-agenda-btn" class="btn-secondary w-full mt-4">
                <i class="fas fa-plus mr-2"></i>Adicionar
            </button>
        </div>
    </div>

    <!-- Segunda Linha: Rituais e Atividades Recentes -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        
        <!-- Card: Rituais -->
        <div class="card">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-xl font-bold text-white">RITUAIS</h3>
                <button id="ritual-settings-btn" class="text-neutral-400 hover:text-white"><i class="fas fa-cog"></i></button>
            </div>
            <div class="flex space-x-2 mb-4">
                <button class="tab active" data-tipo="matinal">Matinal</button>
                <button class="tab" data-tipo="noturno">Noturno</button>
            </div>
            <div id="rituais-list" class="mb-4 min-h-[100px]">
                <p class="text-sm text-neutral-500 text-center py-4">Você não possui nenhum item adicionado aqui</p>
            </div>
            <button id="add-ritual-btn" class="btn-secondary w-full">
                <i class="fas fa-plus mr-2"></i>Novo Item
            </button>
        </div>

        <!-- Card: Atividades Recentes -->
        <div class="card">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-xl font-bold text-white">Atividades Recentes</h3>
                <i class="fas fa-history text-neutral-400"></i>
            </div>
            <div class="space-y-3">
                @if(count($atividadesRecentes) > 0)
                    @foreach($atividadesRecentes as $atividade)
                    <div class="flex items-center space-x-3 p-3 bg-neutral-800 rounded-lg">
                        <div class="w-10 h-10 bg-red-500/20 rounded-lg flex items-center justify-center">
                            <i class="fas fa-{{ $atividade['icon'] }} text-red-500"></i>
                        </div>
                        <div class="flex-1">
                            <p class="text-sm font-medium text-white">{{ $atividade['titulo'] }}</p>
                            <p class="text-xs text-neutral-400">{{ \Carbon\Carbon::parse($atividade['data'])->diffForHumans() }}</p>
                        </div>
                    </div>
                    @endforeach
                @else
                    <div class="text-center py-8">
                        <i class="fas fa-inbox text-4xl text-neutral-600 mb-3"></i>
                        <p class="text-sm text-neutral-500">Nenhuma atividade recente</p>
                    </div>
                @endif
            </div>
        </div>
    </div>

</div>

<!-- Modal Ritual -->
<div id="ritual-modal" class="modal">
    <div class="modal-content">
        <button class="close-btn" onclick="closeModal('ritual-modal')">&times;</button>
        <h2 class="text-2xl font-bold mb-4" id="ritual-modal-title">Novo Ritual</h2>
        <form id="ritual-form">
            <input type="hidden" id="ritual-id">
            <input type="hidden" id="ritual-tipo" value="matinal">
            <div class="mb-4">
                <label class="block text-sm font-medium mb-2">Nome</label>
                <input type="text" id="ritual-nome" class="form-input" required>
            </div>
            <div class="mb-4">
                <label class="block text-sm font-medium mb-2">Horário Início</label>
                <input type="time" id="ritual-horario-inicio" class="form-input" required>
            </div>
            <div class="mb-4">
                <label class="block text-sm font-medium mb-2">Horário Fim (opcional)</label>
                <input type="time" id="ritual-horario-fim" class="form-input">
            </div>
            <div class="mb-4">
                <label class="block text-sm font-medium mb-2">Descrição</label>
                <textarea id="ritual-descricao" class="form-input" rows="3"></textarea>
            </div>
            <button type="submit" class="btn-primary w-full">Salvar</button>
        </form>
    </div>
</div>

<!-- Modal Agenda Event -->
<div id="agenda-event-modal" class="modal">
    <div class="modal-content">
        <button class="close-btn" onclick="closeModal('agenda-event-modal')">&times;</button>
        <h2 class="text-2xl font-bold mb-4" id="agenda-event-title">Detalhes do Compromisso</h2>
        <div id="agenda-event-content"></div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    const API_BASE_URL = window.location.origin + '/api';
    let currentRitualType = 'matinal';

    async function fetchAPI(endpoint, options = {}) {
        try {
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
                mode: 'cors', 
                ...options
            });
            if (!response.ok) throw new Error(`Erro: ${response.status} ${response.statusText}`);
            if (response.status === 204 || (response.status === 200 && options.method === 'DELETE')) return { status: true };
            const contentType = response.headers.get('content-type');
            if (contentType && contentType.includes('application/json')) {
                return await response.json();
            }
            return { status: true };
        } catch (error) {
            console.error('Erro:', error);
            return null;
        }
    }

    async function loadRituais() {
        const response = await fetchAPI(`/ritual?tipo=${currentRitualType}`);
        const rituais = response?.data || [];
        const container = document.getElementById('rituais-list');
        
        if (rituais.length === 0) {
            container.innerHTML = '<p class="text-sm text-neutral-500 text-center py-4">Você não possui nenhum item adicionado aqui</p>';
            return;
        }
        
        container.innerHTML = rituais.map(r => `
            <div class="flex items-center justify-between p-3 bg-neutral-800 rounded-lg mb-2">
                <div class="flex-1">
                    <p class="text-white font-medium">${r.nome || 'Sem nome'}</p>
                    ${r.descricao ? `<p class="text-xs text-neutral-400 mt-1">${r.descricao}</p>` : ''}
                </div>
                <button onclick="deleteRitual(${r.id})" class="text-red-500 hover:text-red-400 ml-2">
                    <i class="fas fa-trash"></i>
                </button>
            </div>
        `).join('');
    }

    async function deleteRitual(id) {
        if (!confirm('Tem certeza que deseja excluir este ritual?')) return;
        await fetchAPI(`/ritual/${id}`, { method: 'DELETE' });
        await loadRituais();
    }

    function openModal(id) {
        document.getElementById(id).classList.add('active');
    }

    function closeModal(id) {
        document.getElementById(id).classList.remove('active');
    }

    document.addEventListener('DOMContentLoaded', () => {
        loadRituais();

        document.querySelectorAll('.tab').forEach(tab => {
            tab.addEventListener('click', () => {
                document.querySelectorAll('.tab').forEach(t => t.classList.remove('active'));
                tab.classList.add('active');
                currentRitualType = tab.dataset.tipo;
                loadRituais();
            });
        });

        document.getElementById('add-ritual-btn')?.addEventListener('click', () => {
            document.getElementById('ritual-modal-title').textContent = 'Novo Ritual';
            document.getElementById('ritual-id').value = '';
            document.getElementById('ritual-tipo').value = currentRitualType;
            document.getElementById('ritual-form').reset();
            openModal('ritual-modal');
        });

        document.getElementById('ritual-form')?.addEventListener('submit', async (e) => {
            e.preventDefault();
            const id = document.getElementById('ritual-id').value;
            const data = {
                nome: document.getElementById('ritual-nome').value,
                tipo: document.getElementById('ritual-tipo').value,
                horario_inicio: document.getElementById('ritual-horario-inicio').value,
                horario_fim: document.getElementById('ritual-horario-fim').value || null,
                descricao: document.getElementById('ritual-descricao').value || null,
            };
            
            const endpoint = id ? `/ritual/${id}` : '/ritual';
            const method = id ? 'PUT' : 'POST';
            
            const response = await fetchAPI(endpoint, {
                method,
                body: JSON.stringify(data)
            });
            
            if (response) {
                closeModal('ritual-modal');
                await loadRituais();
            }
        });

        document.getElementById('add-agenda-btn')?.addEventListener('click', () => {
            window.location.href = '/agenda';
        });
    });
</script>
@endpush
