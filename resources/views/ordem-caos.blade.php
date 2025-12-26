@extends('layouts.app')

@section('title', 'Ordem no Caos')

@push('styles')
<style>
    .pill { display: inline-block; padding: 0.5rem 1rem; border-radius: 9999px; font-size: 0.875rem; font-weight: 600; }
    .pill-red { background: #ef4444; color: white; }
    .timer-display { font-size: 4rem; font-weight: 900; font-variant-numeric: tabular-nums; }
    .task-card { background: #27272a; border: 1px solid #3f3f46; border-radius: 8px; padding: 1rem; margin-bottom: 0.75rem; cursor: move; }
    .task-card:hover { border-color: #71717a; }
    .task-priority-baixa { border-left: 4px solid #10b981; }
    .task-priority-media { border-left: 4px solid #f59e0b; }
    .task-priority-alta { border-left: 4px solid #ef4444; }
    .column { background: rgba(24, 24, 27, 0.6); border: 1px solid #3f3f46; border-radius: 12px; padding: 1rem; min-height: 400px; }
    .column-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 1rem; padding-bottom: 0.5rem; border-bottom: 1px solid #3f3f46; }
    .modal {
        position: fixed; top: 0; left: 0; width: 100%; height: 100%;
        display: flex; align-items: center; justify-content: center;
        background-color: rgba(0, 0, 0, 0.8); backdrop-filter: blur(8px);
        z-index: 1000; opacity: 0; visibility: hidden; transition: opacity 0.3s;
    }
    .modal.active { opacity: 1; visibility: visible; }
    .modal-content {
        background: #18181b; border: 1px solid #3f3f46; border-radius: 12px;
        padding: 2rem; width: 90%; max-width: 600px; position: relative; max-height: 90vh; overflow-y: auto;
    }
    .close-btn { position: absolute; top: 1rem; right: 1rem; background: none; border: none; color: #71717a; font-size: 1.5rem; cursor: pointer; }
    .form-input { background: #27272a; border: 1px solid #3f3f46; border-radius: 8px; padding: 0.75rem; color: white; width: 100%; }
    .form-input:focus { outline: none; border-color: #3b82f6; }
    .drop-zone { min-height: 200px; border: 2px dashed #3f3f46; border-radius: 8px; padding: 1rem; }
    .drop-zone.drag-over { border-color: #3b82f6; background: rgba(59, 130, 246, 0.1); }
</style>
@endpush

@section('content')
<div class="container mx-auto px-4 py-8">
        <!-- Top Section: Pomodoro Timer -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-8">
            <!-- Timer -->
            <div class="card">
                <div class="flex items-center justify-between mb-4">
                    <span class="pill pill-red">POMODORO</span>
                    <div class="flex items-center gap-2">
                        <select id="pomodoro-mode" class="form-input" style="width: auto; padding: 0.5rem;">
                            <option value="produtividade">Produtividade</option>
                            <option value="estudos">Estudos</option>
                            <option value="descanso">Descanso</option>
                        </select>
                        <button id="pomodoro-settings" class="text-neutral-400 hover:text-white">
                            <i class="fas fa-cog"></i>
                        </button>
                    </div>
                </div>
                <div class="text-center">
                    <div class="timer-display mb-6" id="timer-display">30:00</div>
                    <button id="timer-start-btn" class="btn-primary text-lg px-8 py-3">
                        <i class="fas fa-play mr-2"></i>Iniciar
                    </button>
                </div>
            </div>

            <!-- Weekly Progress -->
            <div class="card">
                <div class="mb-4">
                    <div class="flex items-center gap-4 mb-4">
                        <div class="flex items-center gap-2">
                            <span class="w-3 h-3 bg-red-500 rounded-full"></span>
                            <span class="text-sm">Produtividade</span>
                        </div>
                        <div class="flex items-center gap-2">
                            <span class="w-3 h-3 bg-yellow-500 rounded-full"></span>
                            <span class="text-sm">Estudos</span>
                        </div>
                        <div class="flex items-center gap-2">
                            <span class="w-3 h-3 bg-neutral-600 rounded-full"></span>
                            <span class="text-sm">Descanso</span>
                        </div>
                    </div>
                    <div class="flex justify-between items-end" style="height: 150px;">
                        <div class="flex flex-col items-center">
                            <div class="w-10 bg-neutral-700 rounded-t" style="height: 20px;"></div>
                            <span class="text-xs text-neutral-500 mt-1">Dom</span>
                        </div>
                        <div class="flex flex-col items-center">
                            <div class="w-10 bg-neutral-700 rounded-t" style="height: 40px;"></div>
                            <span class="text-xs text-neutral-500 mt-1">Seg</span>
                        </div>
                        <div class="flex flex-col items-center">
                            <div class="w-10 bg-neutral-700 rounded-t" style="height: 30px;"></div>
                            <span class="text-xs text-neutral-500 mt-1">Ter</span>
                        </div>
                        <div class="flex flex-col items-center">
                            <div class="w-10 bg-neutral-700 rounded-t" style="height: 50px;"></div>
                            <span class="text-xs text-neutral-500 mt-1">Qua</span>
                        </div>
                        <div class="flex flex-col items-center">
                            <div class="w-10 bg-neutral-700 rounded-t" style="height: 35px;"></div>
                            <span class="text-xs text-neutral-500 mt-1">Qui</span>
                        </div>
                        <div class="flex flex-col items-center">
                            <div class="w-10 bg-neutral-700 rounded-t" style="height: 45px;"></div>
                            <span class="text-xs text-neutral-500 mt-1">Sex</span>
                        </div>
                        <div class="flex flex-col items-center">
                            <div class="w-10 bg-neutral-700 rounded-t" style="height: 25px;"></div>
                            <span class="text-xs text-neutral-500 mt-1">Sáb</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Bottom Section: Task Board -->
        <div class="mb-6">
            <h2 class="text-3xl font-black text-white mb-6">Quadro de tarefas</h2>
            <div id="task-board" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4"></div>
            <button id="add-column-btn" class="mt-4 text-red-400 hover:text-red-300">
                <i class="fas fa-plus mr-2"></i>Nova coluna
            </button>
        </div>
    </main>

    <!-- Modal Tarefa -->
    <div id="tarefa-modal" class="modal">
        <div class="modal-content">
            <button class="close-btn" onclick="closeModal('tarefa-modal')">&times;</button>
            <h2 class="text-2xl font-bold mb-4" id="tarefa-modal-title">Nova Tarefa</h2>
            <form id="tarefa-form">
                <input type="hidden" id="tarefa-id">
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium mb-2">Título</label>
                        <input type="text" id="tarefa-titulo" class="form-input" required>
                    </div>
                    <div>
                        <label class="block text-sm font-medium mb-2">Descrição</label>
                        <textarea id="tarefa-descricao" class="form-input" rows="3"></textarea>
                    </div>
                    <div>
                        <label class="block text-sm font-medium mb-2">Prioridade</label>
                        <select id="tarefa-prioridade" class="form-input">
                            <option value="baixa">Baixa</option>
                            <option value="media" selected>Média</option>
                            <option value="alta">Alta</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium mb-2">Coluna</label>
                        <select id="tarefa-coluna" class="form-input" required></select>
                    </div>
                    <button type="submit" class="btn-primary w-full">Salvar</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal Coluna -->
    <div id="coluna-modal" class="modal">
        <div class="modal-content">
            <button class="close-btn" onclick="closeModal('coluna-modal')">&times;</button>
            <h2 class="text-2xl font-bold mb-4">Nova Coluna</h2>
            <form id="coluna-form">
                <input type="hidden" id="coluna-id">
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium mb-2">Nome</label>
                        <input type="text" id="coluna-nome" class="form-input" required>
                    </div>
                    <button type="submit" class="btn-primary w-full">Salvar</button>
                </div>
            </form>
        </div>
    </div>
@endsection

@push('scripts')
<script>
    const API_BASE_URL = `${window.location.origin}/api`;
        let timerInterval = null;
        let timerSeconds = 30 * 60; // 30 minutos default
        let isTimerRunning = false;
        let columns = [];
        let tasks = [];

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
                    mode: 'cors', ...options
                });
                if (!response.ok) throw new Error(`Erro: ${response.statusText}`);
                if (response.status === 204 || (response.status === 200 && options.method === 'DELETE')) return { status: true };
                return await response.json();
            } catch (error) {
                console.error('Erro:', error);
                alert('Erro ao conectar com a API');
                return null;
            }
        }

        function formatTime(seconds) {
            const mins = Math.floor(seconds / 60);
            const secs = seconds % 60;
            return `${mins.toString().padStart(2, '0')}:${secs.toString().padStart(2, '0')}`;
        }

        function updateTimerDisplay() {
            document.getElementById('timer-display').textContent = formatTime(timerSeconds);
        }

        function startTimer() {
            if (isTimerRunning) {
                clearInterval(timerInterval);
                isTimerRunning = false;
                document.getElementById('timer-start-btn').innerHTML = '<i class="fas fa-play mr-2"></i>Iniciar';
            } else {
                isTimerRunning = true;
                document.getElementById('timer-start-btn').innerHTML = '<i class="fas fa-pause mr-2"></i>Pausar';
                timerInterval = setInterval(() => {
                    timerSeconds--;
                    updateTimerDisplay();
                    if (timerSeconds <= 0) {
                        clearInterval(timerInterval);
                        isTimerRunning = false;
                        alert('Pomodoro concluído!');
                        savePomodoro();
                        timerSeconds = 30 * 60;
                        updateTimerDisplay();
                        document.getElementById('timer-start-btn').innerHTML = '<i class="fas fa-play mr-2"></i>Iniciar';
                    }
                }, 1000);
            }
        }

        async function savePomodoro() {
            const tipo = document.getElementById('pomodoro-mode').value;
            await fetchAPI('/pomodoro', {
                method: 'POST',
                body: JSON.stringify({
                    data: new Date().toISOString().split('T')[0],
                    duracao_minutos: 30,
                    tipo: tipo,
                    concluido: true
                })
            });
        }

        async function loadColumns() {
            const res = await fetchAPI('/tarefa-coluna');
            if (res?.data) {
                columns = res.data;
                // Criar colunas padrão se não existirem
                if (columns.length === 0) {
                    const defaultColumns = ['Pendentes', 'Em Andamento', 'Concluídas'];
                    for (const nome of defaultColumns) {
                        await fetchAPI('/tarefa-coluna', {
                            method: 'POST',
                            body: JSON.stringify({ nome })
                        });
                    }
                    loadColumns();
                    return;
                }
                renderBoard();
                loadTasks();
            }
        }

        async function loadTasks() {
            const res = await fetchAPI('/tarefa');
            if (res?.data) {
                tasks = res.data;
                renderBoard();
            }
        }

        function renderBoard() {
            const boardEl = document.getElementById('task-board');
            boardEl.innerHTML = columns.map(col => {
                const colTasks = tasks.filter(t => t.tarefa_coluna_id == col.id);
                return `
                    <div class="column" data-col-id="${col.id}">
                        <div class="column-header">
                            <h3 class="font-bold text-lg">${col.nome.toUpperCase()}</h3>
                            <div class="flex gap-2">
                                <button onclick="editColumn(${col.id})" class="text-neutral-400 hover:text-white">
                                    <i class="fas fa-ellipsis-v"></i>
                                </button>
                            </div>
                        </div>
                        <div class="drop-zone" ondrop="dropTask(event, ${col.id})" ondragover="allowDrop(event)" ondragenter="dragEnter(event)" ondragleave="dragLeave(event)">
                            ${colTasks.map(t => renderTaskCard(t)).join('')}
                            ${colTasks.length === 0 && col.nome.toLowerCase() === 'concluídas' ? '<p class="text-center text-neutral-500 py-8">Solte cards aqui</p>' : ''}
                            <button onclick="openTarefaModal(${col.id})" class="text-red-400 hover:text-red-300 text-sm w-full mt-2">
                                <i class="fas fa-plus mr-2"></i>Adicionar um card
                            </button>
                        </div>
                    </div>
                `;
            }).join('');
            
            // Adicionar botão de nova coluna
            boardEl.innerHTML += `
                <div class="column border-dashed border-2 border-neutral-600 flex items-center justify-center cursor-pointer" onclick="openColunaModal()">
                    <div class="text-center">
                        <i class="fas fa-plus text-4xl text-neutral-600 mb-2"></i>
                        <p class="text-neutral-500">Nova coluna</p>
                    </div>
                </div>
            `;
        }

        function renderTaskCard(task) {
            const prioridadeClass = `task-priority-${task.prioridade}`;
            return `
                <div class="task-card ${prioridadeClass}" draggable="true" ondragstart="dragStart(event, ${task.id})" data-task-id="${task.id}">
                    <div class="flex items-center justify-between mb-2">
                        <span class="pill ${task.prioridade === 'alta' ? 'pill-red' : task.prioridade === 'media' ? 'bg-orange-500' : 'bg-green-500'}" style="font-size: 0.75rem; padding: 0.25rem 0.5rem;">
                            PRIORIDADE ${task.prioridade.toUpperCase()}
                        </span>
                        <div class="flex gap-2">
                            <button onclick="editTarefa(${task.id})" class="text-blue-400 hover:text-blue-300">
                                <i class="fas fa-pencil"></i>
                            </button>
                            <button onclick="deleteTarefa(${task.id})" class="text-red-400 hover:text-red-300">
                                <i class="fas fa-ellipsis-v"></i>
                            </button>
                        </div>
                    </div>
                    <p class="font-bold">${task.titulo}</p>
                    ${task.descricao ? `<p class="text-sm text-neutral-400 mt-1">${task.descricao}</p>` : ''}
                </div>
            `;
        }

        function allowDrop(ev) {
            ev.preventDefault();
        }

        function dragEnter(ev) {
            ev.preventDefault();
            ev.currentTarget.classList.add('drag-over');
        }

        function dragLeave(ev) {
            ev.currentTarget.classList.remove('drag-over');
        }

        function dragStart(ev, taskId) {
            ev.dataTransfer.setData('taskId', taskId);
        }

        async function dropTask(ev, colunaId) {
            ev.preventDefault();
            ev.currentTarget.classList.remove('drag-over');
            const taskId = ev.dataTransfer.getData('taskId');
            const res = await fetchAPI(`/tarefa/${taskId}/move`, {
                method: 'POST',
                body: JSON.stringify({ tarefa_coluna_id: colunaId })
            });
            if (res?.status || res?.tarefa) {
                loadTasks();
            }
        }

        function openTarefaModal(colunaId) {
            document.getElementById('tarefa-modal-title').textContent = 'Nova Tarefa';
            document.getElementById('tarefa-form').reset();
            document.getElementById('tarefa-id').value = '';
            document.getElementById('tarefa-coluna').value = colunaId;
            populateColunaSelect();
            document.getElementById('tarefa-modal').classList.add('active');
        }

        function openColunaModal() {
            document.getElementById('coluna-form').reset();
            document.getElementById('coluna-id').value = '';
            document.getElementById('coluna-modal').classList.add('active');
        }

        function populateColunaSelect() {
            const select = document.getElementById('tarefa-coluna');
            select.innerHTML = columns.map(c => `<option value="${c.id}">${c.nome}</option>`).join('');
        }

        async function saveTarefa(e) {
            e.preventDefault();
            const id = document.getElementById('tarefa-id').value;
            const data = {
                titulo: document.getElementById('tarefa-titulo').value,
                descricao: document.getElementById('tarefa-descricao').value || null,
                prioridade: document.getElementById('tarefa-prioridade').value,
                tarefa_coluna_id: parseInt(document.getElementById('tarefa-coluna').value),
            };
            const endpoint = id ? `/tarefa/${id}` : '/tarefa';
            const method = id ? 'PUT' : 'POST';
            const res = await fetchAPI(endpoint, { method, body: JSON.stringify(data) });
            if (res?.status || res?.tarefa) {
                closeModal('tarefa-modal');
                loadTasks();
            }
        }

        async function editTarefa(id) {
            const res = await fetchAPI(`/tarefa/${id}`);
            if (res?.data) {
                const t = res.data;
                document.getElementById('tarefa-id').value = t.id;
                document.getElementById('tarefa-titulo').value = t.titulo;
                document.getElementById('tarefa-descricao').value = t.descricao || '';
                document.getElementById('tarefa-prioridade').value = t.prioridade;
                populateColunaSelect();
                document.getElementById('tarefa-coluna').value = t.tarefa_coluna_id;
                document.getElementById('tarefa-modal-title').textContent = 'Editar Tarefa';
                document.getElementById('tarefa-modal').classList.add('active');
            }
        }

        async function deleteTarefa(id) {
            if (confirm('Deseja deletar esta tarefa?')) {
                const res = await fetchAPI(`/tarefa/${id}`, { method: 'DELETE' });
                if (res?.status) loadTasks();
            }
        }

        async function saveColuna(e) {
            e.preventDefault();
            const id = document.getElementById('coluna-id').value;
            const data = {
                nome: document.getElementById('coluna-nome').value,
            };
            const endpoint = id ? `/tarefa-coluna/${id}` : '/tarefa-coluna';
            const method = id ? 'PUT' : 'POST';
            const res = await fetchAPI(endpoint, { method, body: JSON.stringify(data) });
            if (res?.status || res?.coluna) {
                closeModal('coluna-modal');
                loadColumns();
            }
        }

        function editColumn(id) {
            const col = columns.find(c => c.id == id);
            if (col) {
                document.getElementById('coluna-id').value = col.id;
                document.getElementById('coluna-nome').value = col.nome;
                document.getElementById('coluna-modal').classList.add('active');
            }
        }

        function closeModal(id) {
            document.getElementById(id).classList.remove('active');
        }

        document.addEventListener('DOMContentLoaded', () => {
            updateTimerDisplay();
            loadColumns();

            document.getElementById('timer-start-btn')?.addEventListener('click', startTimer);
            document.getElementById('tarefa-form')?.addEventListener('submit', saveTarefa);
            document.getElementById('coluna-form')?.addEventListener('submit', saveColuna);
        });
</script>
@endpush

