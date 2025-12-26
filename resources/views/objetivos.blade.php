@extends('layouts.app')

@section('title', 'Metas e Objetivos')

@push('styles')
<style>
        /* Cor de destaque para Objetivos: Vermelho (Tema Consistente) */
        .goals-color {
            color: #EF4444; /* Vermelho vibrante */
        }
        .goals-bg-color {
            background-color: #EF4444; /* Vermelho vibrante */
        }


        /* Estilo dos Cards de Objetivo (mantendo a base NUTRI.AI) */
        .goal-card {
            background: rgba(16, 16, 16, 0.6);
            border: 1px solid #262626;
            backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(12px);
            transition: all 0.3s ease;
            position: relative;
        }
        .goal-card:hover {
            transform: translateY(-5px);
            border-color: rgba(239, 68, 68, 0.5); /* Borda Vermelha no hover */
        }
        
        /* Barra de Progresso Customizada */
        .progress-bar-container {
            height: 10px;
            background-color: #374151;
            border-radius: 5px;
            overflow: hidden;
        }
        .progress-bar {
            height: 100%;
            background-color: #EF4444; /* Vermelho */
            transition: width 0.5s ease-out;
        }
        
        /* Estilo do Modal (Reutilizando a correção anterior) */
        .modal {
            position: fixed; top: 0; left: 0; width: 100%; height: 100%;
            display: flex; align-items: center; justify-content: center;
            background-color: rgba(0, 0, 0, 0.85); backdrop-filter: blur(10px);
            z-index: 1000;
            opacity: 0; visibility: hidden; transition: opacity 0.3s, visibility 0.3s;
        }
        .modal.active { opacity: 1; visibility: visible; }
        .modal-content {
            background: #18181b; border: 1px solid #3f3f46; border-radius: 12px;
            padding: 2rem; width: 90%; max-width: 600px;
            position: relative;
            transform: scale(0.95); transition: transform 0.3s;
            box-shadow: 0 20px 25px -5px rgb(0 0 0 / 0.4), 0 8px 10px -6px rgb(0 0 0 / 0.4);
        }
        .modal.active .modal-content { transform: scale(1); }
        .close-button {
            position: absolute; top: 1rem; right: 1rem; background: none; border: none;
            color: #71717a; font-size: 1.5rem; cursor: pointer; transition: color 0.3s;
        }
        .close-button:hover { color: white; }

        /* Inputs do Formulário */
        .form-input, .form-select {
            background-color: #27272a;
            border: 1px solid #3f3f46;
            border-radius: 0.5rem;
            padding: 0.75rem 1rem;
            color: white;
            transition: border-color 0.2s, box-shadow 0.2s;
            -webkit-appearance: none; -moz-appearance: none; appearance: none;
        }
        .form-input:focus, .form-select:focus {
            outline: none;
            border-color: #EF4444; /* Foco Vermelho */
            box-shadow: 0 0 0 2px rgba(239, 68, 68, 0.5);
        }
        .form-select {
           background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 20 20'%3e%3cpath stroke='%239ca3af' stroke-linecap='round' stroke-linejoin='round' stroke-width='1.5' d='M6 8l4 4 4-4'/%3e%3c/svg%3e");
           background-position: right 0.5rem center;
           background-repeat: no-repeat;
           background-size: 1.5em 1.5em;
           padding-right: 2.5rem;
        }

        /* Botão de Ação principal (Vermelho) */
        .primary-action-btn {
            background-color: #EF4444; 
            color: white; 
            transition: background-color 0.2s;
        }
        .primary-action-btn:hover {
            background-color: #DC2626;
        }
        
    </style>
@endpush

@section('content')
<div class="container mx-auto p-4 md:p-6 lg:p-8">
    <!-- Header (Estilo NUTRI.AI) -->
    <header class="container mx-auto p-4 md:p-6 lg:p-8 flex justify-between items-center">
        <div class="flex items-center space-x-3">
            <i class="fas fa-bullseye text-3xl goals-color"></i> 
            <h1 class="text-2xl font-black text-white uppercase tracking-wider">Metas e Objetivos</h1>
        </div>
        <div class="flex items-center space-x-3">
            <button id="add-goal-btn" class="primary-action-btn font-bold py-2 px-4 rounded-lg text-sm transition-transform hover:scale-105 flex items-center justify-center space-x-2">
                <i class="fas fa-plus"></i>
                <span>Novo Objetivo</span>
            </button>
        </div>
    </header>

    <!-- Conteúdo Principal -->
    <main class="container mx-auto p-4 md:p-6 lg:p-8 pt-0">
        
        <!-- Dashboard de Estatísticas Rápido -->
        <section class="grid grid-cols-2 md:grid-cols-4 gap-6 mb-8">
            <div class="goal-card p-4 rounded-lg text-center">
                <p class="text-sm font-medium text-neutral-400">Objetivos Ativos</p>
                <p class="text-3xl font-black goals-color" id="active-goals-count">0</p>
            </div>
            <div class="goal-card p-4 rounded-lg text-center">
                <p class="text-sm font-medium text-neutral-400">Próximo Prazo</p>
                <p class="text-3xl font-black text-white" id="next-deadline">N/A</p>
            </div>
            <div class="goal-card p-4 rounded-lg text-center col-span-2 md:col-span-1">
                <p class="text-sm font-medium text-neutral-400">Lembretes Pendentes</p>
                <p class="text-3xl font-black text-yellow-500" id="pending-reminders-count">0</p>
            </div>
             <div class="goal-card p-4 rounded-lg text-center col-span-2 md:col-span-1">
                <p class="text-sm font-medium text-neutral-400">Concluídos (Total)</p>
                <p class="text-3xl font-black text-green-500" id="completed-goals-count">0</p>
            </div>
        </section>

        <!-- Filtros e Ordenação -->
        <div class="flex flex-col md:flex-row justify-between items-center mb-6 space-y-4 md:space-y-0">
            <h2 class="text-2xl font-bold text-white">Meus Objetivos Ativos</h2>
            <div class="flex space-x-3">
                <select id="filter-topic" class="form-select bg-gray-800 border-gray-700 text-white text-sm rounded-lg p-2.5">
                    <option value="all">Todos os Tópicos</option>
                    <option value="saude">Saúde</option>
                    <option value="financas">Finanças</option>
                    <option value="carreira">Carreira</option>
                    <option value="pessoal">Pessoal</option>
                </select>
                <select id="sort-order" class="form-select bg-gray-800 border-gray-700 text-white text-sm rounded-lg p-2.5">
                    <option value="deadline">Prazo (Próximo)</option>
                    <option value="progress">Progresso (%)</option>
                    <option value="recent">Mais Recente</option>
                </select>
            </div>
        </div>

        <!-- Área de Listagem de Objetivos -->
        <section id="goals-list-container" class="grid grid-cols-1 lg:grid-cols-2 xl:grid-cols-3 gap-6">
            <!-- Os cards de objetivos serão inseridos aqui pelo JS -->
        </section>
        
</div>

    <!-- --- MODAL PARA ADICIONAR/EDITAR OBJETIVO --- -->
    <div id="goal-modal" class="modal"> 
        <div class="modal-content max-h-[90vh] flex flex-col"> 
            <button class="close-button" data-modal-id="goal-modal">&times;</button> 
            
            <h2 id="goal-modal-title" class="text-2xl font-bold text-white mb-6 flex-shrink-0">Novo Objetivo</h2> 
            
            <form id="goal-form" class="flex-grow overflow-y-auto pr-4 space-y-4"> 
                <input type="hidden" id="goal-id"> 
                
                <div>
                    <label for="goal-title" class="block text-sm font-medium text-neutral-300 mb-1">Título do Objetivo</label>
                    <input type="text" id="goal-title" placeholder="Ex: Correr 5km sem parar" class="mt-1 w-full form-input" required>
                </div> 
                
                <div class="grid grid-cols-2 gap-4"> 
                    <div> 
                        <label for="goal-topic" class="block text-sm font-medium text-neutral-300 mb-1">Tópico</label> 
                        <select id="goal-topic" class="mt-1 w-full form-select" required> 
                            <option value="saude">Saúde</option>
                            <option value="financas">Finanças</option>
                            <option value="carreira">Carreira</option>
                            <option value="pessoal">Pessoal</option>
                            <option value="outros">Outros</option>
                        </select> 
                    </div> 
                    <div> 
                        <label for="goal-deadline" class="block text-sm font-medium text-neutral-300 mb-1">Prazo Final</label> 
                        <input type="date" id="goal-deadline" class="mt-1 w-full form-input" required>
                    </div> 
                </div> 
                
                <div>
                    <label for="goal-description" class="block text-sm font-medium text-neutral-300 mb-1">Descrição</label>
                    <textarea id="goal-description" rows="2" class="mt-1 w-full form-input" placeholder="O que você quer alcançar?"></textarea>
                </div> 

                <!-- Secção de Lembrtes/Ações (Checklist) -->
                <div class="border-t border-neutral-700 pt-4 space-y-3">
                    <h3 class="text-lg font-bold text-white flex justify-between items-center">
                        Lembretes / Ações
                        <button type="button" id="add-reminder-btn" class="text-sm goals-color hover:text-red-400 transition"><i class="fas fa-plus mr-1"></i> Adicionar</button>
                    </h3>
                    <div id="reminders-list" class="space-y-2">
                        <!-- Reminders serão injetados aqui -->
                    </div>
                </div>

                <div class="flex items-center pt-2">
                    <input type="checkbox" id="goal-completed" class="h-4 w-4 rounded border-gray-300 goals-color focus:ring-red-500 bg-neutral-700 border-neutral-600">
                    <label for="goal-completed" class="ml-2 block text-sm text-neutral-300">Marcar como Concluído</label>
                </div>
                
                <!-- Botão de Submissão -->
                <div class="pt-6 border-t border-neutral-700 flex-shrink-0">
                    <button type="submit" class="w-full primary-action-btn font-bold py-3 rounded-lg transition">Guardar Objetivo</button>
                </div> 
            </form> 
        </div> 
    </div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', () => {
            
            // --- Seletores DOM ---
            const addGoalBtn = document.getElementById('add-goal-btn');
            const goalModal = document.getElementById('goal-modal');
            const goalForm = document.getElementById('goal-form');
            const goalsListContainer = document.getElementById('goals-list-container');
            const remindersList = document.getElementById('reminders-list');
            const addReminderBtn = document.getElementById('add-reminder-btn');
            const activeGoalsCountEl = document.getElementById('active-goals-count');
            const nextDeadlineEl = document.getElementById('next-deadline');
            const pendingRemindersCountEl = document.getElementById('pending-reminders-count');
            const completedGoalsCountEl = document.getElementById('completed-goals-count');

                        // --- dados carregados via API ---
                        let goalsData = [];
            
            // --- Funções Auxiliares ---

            function openModal(modal) { modal.classList.add('active'); }
            function closeModal(modal) { 
                modal.classList.remove('active');
                goalForm.reset(); 
                remindersList.innerHTML = '';
            }
            
            function formatDate(dateString) {
                const options = { year: 'numeric', month: 'short', day: 'numeric' };
                const date = new Date(dateString + 'T00:00:00'); // Trata como data local
                if(isNaN(date)) return 'Data Inválida';
                return date.toLocaleDateString('pt-BR', options);
            }
            
            function getIconForTopic(topic) {
                switch(topic) {
                    case 'saude': return 'fas fa-heart-pulse';
                    case 'financas': return 'fas fa-wallet';
                    case 'carreira': return 'fas fa-briefcase';
                    case 'pessoal': return 'fas fa-star';
                    default: return 'fas fa-lightbulb';
                }
            }

            function getProgress(reminders = []) {
                if (reminders.length === 0) return 0;
                const completed = reminders.filter(r => r.completed).length;
                return Math.round((completed / reminders.length) * 100);
            }
            
            // --- Funções de Renderização de UI ---

            function renderStatistics(goals) {
                const activeGoals = goals.filter(g => !g.completed);
                const completedGoals = goals.filter(g => g.completed);
                
                let pendingReminders = 0;
                activeGoals.forEach(g => {
                    pendingReminders += g.reminders.filter(r => !r.completed).length;
                });
                
                // Encontra o próximo prazo
                const now = new Date();
                const upcomingGoals = activeGoals
                    .filter(g => g.deadline && g.deadline.trim() !== '') // Filtra apenas objetivos com deadline válido
                    .filter(g => {
                        try {
                            const deadlineDate = new Date(g.deadline + 'T23:59:59');
                            return !isNaN(deadlineDate) && deadlineDate >= now;
                        } catch (e) {
                            return false;
                        }
                    })
                    .sort((a, b) => {
                        try {
                            return new Date(a.deadline) - new Date(b.deadline);
                        } catch (e) {
                            return 0;
                        }
                    });
                
                const nextDeadline = upcomingGoals.length > 0 ? formatDate(upcomingGoals[0].deadline) : 'N/A';

                // Atualiza o DOM
                activeGoalsCountEl.textContent = activeGoals.length;
                completedGoalsCountEl.textContent = completedGoals.length;
                pendingRemindersCountEl.textContent = pendingReminders;
                nextDeadlineEl.textContent = nextDeadline;
            }

            async function fetchGoals() {
                const res = await fetch('/api/objetivo', {
                    credentials: 'include',
                    headers: {
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                });
                if (!res.ok) return [];
                const data = await res.json();
                return Array.isArray(data) ? data : (data.data || []);
            }

            async function loadAndRender() {
                goalsData = await fetchGoals();
                renderGoals(goalsData);
            }


            function renderGoals(goals) {
                goalsListContainer.innerHTML = '';
                
                const goalsToDisplay = goals.filter(g => !g.completed); // Mostrar apenas ativos no painel principal

                if (goalsToDisplay.length === 0) {
                    goalsListContainer.innerHTML = `<div class="lg:col-span-2 xl:col-span-3 text-center text-neutral-600 border-2 border-dashed border-neutral-800 rounded-lg p-12">
                        <i class="fas fa-check-circle fa-3x goals-color mb-3"></i>
                        <p class="font-bold text-lg text-white">Todos os seus objetivos ativos foram concluídos! Parabéns!</p>
                        <p class="text-sm mt-1">Clique em "Novo Objetivo" para começar uma nova jornada.</p>
                    </div>`;
                    return;
                }

                goalsToDisplay.forEach(goal => {
                    const progress = getProgress(goal.reminders);
                    const progressColor = progress === 100 ? 'bg-green-500' : 'goals-bg-color'; // Usa a cor de destaque (Vermelho) para progresso em andamento
                    const iconClass = getIconForTopic(goal.topic);
                    const remainingDays = Math.ceil((new Date(goal.deadline + 'T23:59:59') - new Date()) / (1000 * 60 * 60 * 24));
                    const daysLeftText = remainingDays >= 0 ? `${remainingDays} dias restantes` : `Atrasado em ${Math.abs(remainingDays)} dias`;
                    const daysLeftClass = remainingDays > 30 ? 'text-neutral-500' : (remainingDays > 0 ? 'text-yellow-500' : 'text-red-500');

                    const card = document.createElement('div');
                    card.className = "goal-card p-5 rounded-xl";
                    card.innerHTML = `
                        <div class="flex justify-between items-start mb-4">
                            <div class="flex items-center space-x-3">
                                <div class="w-10 h-10 rounded-full bg-neutral-800 flex items-center justify-center goals-color border border-neutral-700">
                                    <i class="${iconClass} fa-lg"></i>
                                </div>
                                <div>
                                    <h3 class="text-lg font-bold text-white truncate">${goal.title}</h3>
                                    <p class="text-xs text-neutral-400 uppercase">${goal.topic}</p>
                                </div>
                            </div>
                            <div class="flex space-x-2">
                                <button class="edit-goal-btn h-8 w-8 rounded-lg text-neutral-400 hover:bg-neutral-700 hover:text-white transition" data-id="${goal.id}" title="Editar Objetivo">
                                    <i class="fas fa-pencil-alt fa-xs"></i>
                                </button>
                                <button class="complete-goal-btn h-8 w-8 rounded-lg text-neutral-400 hover:bg-neutral-700 hover:text-green-500 transition" data-id="${goal.id}" title="Concluir Objetivo">
                                    <i class="fas fa-check fa-xs"></i>
                                </button>
                            </div>
                        </div>

                        <p class="text-sm text-neutral-300 mb-4">${goal.description || 'Sem descrição.'}</p>

                        <div class="progress-bar-container mb-2">
                            <div class="progress-bar ${progressColor}" style="width: ${progress}%;"></div>
                        </div>
                        <div class="flex justify-between items-center text-xs">
                            <span class="font-semibold text-white">${progress}% Progresso</span>
                            <span class="${daysLeftClass} font-medium"><i class="fas fa-calendar-alt mr-1"></i> ${daysLeftText}</span>
                        </div>
                        
                        <!-- Lembretes (Mini-Checklist) -->
                        <div class="mt-4 pt-4 border-t border-neutral-800">
                            <h4 class="text-xs font-bold text-neutral-400 uppercase mb-2">Próximas Ações (${goal.reminders.filter(r => !r.completed).length} pendentes)</h4>
                            <ul class="space-y-1 max-h-24 overflow-y-auto">
                                ${goal.reminders.slice(0, 3).map(r => `
                                    <li class="text-sm text-neutral-300 flex items-center space-x-2 truncate">
                                        <input type="checkbox" data-goal-id="${goal.id}" data-reminder-id="${r.id}" ${r.completed ? 'checked' : ''} class="reminder-checkbox h-4 w-4 rounded border-gray-300 text-red-600 focus:ring-red-500 bg-neutral-700 border-neutral-600">
                                        <span class="${r.completed ? 'line-through text-neutral-500' : ''}">${r.text}</span>
                                    </li>
                                `).join('')}
                            </ul>
                            ${goal.reminders.length > 0 ? `<button class="view-reminders-btn text-xs goals-color mt-2 hover:text-red-400 transition" data-id="${goal.id}"><i class="fas fa-list-check mr-1"></i> Gerenciar Lembretes (${goal.reminders.length})</button>` : `<p class="text-xs text-neutral-500 mt-2">Nenhum lembrete definido. Adicione um para acompanhar o progresso.</p>`}
                        </div>

                    `;
                    goalsListContainer.appendChild(card);
                });
                
                renderStatistics(goals);
            }
            
            function renderReminderInput(reminder = { text: '', completed: false }) {
                const div = document.createElement('div');
                div.className = 'flex items-center space-x-2 p-2 bg-neutral-800 rounded-md';
                div.innerHTML = `
                    <input type="checkbox" ${reminder.completed ? 'checked' : ''} data-field="completed" class="h-4 w-4 rounded border-gray-300 text-red-600 focus:ring-red-500 bg-neutral-700 border-neutral-600">
                    <input type="text" value="${reminder.text}" data-field="text" placeholder="O que você precisa fazer?" class="flex-grow form-input p-1 text-sm bg-transparent border-none focus:ring-0" required>
                    <button type="button" class="remove-reminder-btn text-neutral-500 hover:text-red-400 w-6 h-6"><i class="fas fa-trash-alt fa-xs"></i></button>
                `;
                return div;
            }


            // --- Lógica de Eventos e Formulário ---

            addGoalBtn.addEventListener('click', () => {
                document.getElementById('goal-modal-title').textContent = 'Novo Objetivo';
                document.getElementById('goal-id').value = '';
                document.getElementById('goal-completed').checked = false;
                remindersList.innerHTML = '';
                addReminderBtn.click(); // Adiciona um lembrete vazio por padrão
                openModal(goalModal);
            });

            goalModal.querySelector('.close-button').addEventListener('click', () => closeModal(goalModal));

            addReminderBtn.addEventListener('click', (e) => {
                e.preventDefault();
                remindersList.appendChild(renderReminderInput());
            });

            remindersList.addEventListener('click', (e) => {
                if (e.target.closest('.remove-reminder-btn')) {
                    e.target.closest('.remove-reminder-btn').parentElement.remove();
                }
            });

            goalsListContainer.addEventListener('click', (e) => {
                const editBtn = e.target.closest('.edit-goal-btn');
                const completeBtn = e.target.closest('.complete-goal-btn');
                const reminderCheckbox = e.target.closest('.reminder-checkbox');
                
                if (editBtn) {
                    const goalId = parseInt(editBtn.dataset.id);
                    const goal = goalsData.find(g => g.id === goalId);
                    if (goal) {
                        openEditGoalModal(goal);
                    }
                } else if (completeBtn) {
                    const goalId = parseInt(completeBtn.dataset.id);
                    // (Substituir 'confirm' por um modal de confirmação)
                    if (confirm('Tem certeza que deseja marcar este objetivo como concluído?')) {
                        toggleGoalCompletion(goalId, true);
                    }
                } else if (reminderCheckbox) {
                    const goalId = parseInt(reminderCheckbox.dataset.goalId);
                    const reminderId = parseInt(reminderCheckbox.dataset.reminderId);
                    const completed = reminderCheckbox.checked;
                    toggleReminderCompletion(goalId, reminderId, completed);
                }
            });

            function openEditGoalModal(goal) {
                document.getElementById('goal-modal-title').textContent = 'Editar Objetivo';
                document.getElementById('goal-id').value = goal.id;
                document.getElementById('goal-title').value = goal.title;
                document.getElementById('goal-topic').value = goal.topic;
                document.getElementById('goal-deadline').value = goal.deadline;
                document.getElementById('goal-description').value = goal.description;
                document.getElementById('goal-completed').checked = goal.completed;

                remindersList.innerHTML = '';
                if (goal.reminders.length > 0) {
                    goal.reminders.forEach(r => remindersList.appendChild(renderReminderInput(r)));
                } else {
                    addReminderBtn.click();
                }
                
                openModal(goalModal);
            }
            
            function collectRemindersFromForm() {
                return Array.from(remindersList.children).map(row => ({
                    id: Date.now() + Math.random(), // Novo ID para novos lembretes
                    text: row.querySelector('[data-field="text"]').value.trim(),
                    completed: row.querySelector('[data-field="completed"]').checked
                })).filter(r => r.text !== ''); // Ignora lembretes vazios
            }
            
            goalForm.addEventListener('submit', (e) => {
                e.preventDefault();

                const id = document.getElementById('goal-id').value;
                const isEditing = !!id;

                const newGoalData = {
                    title: document.getElementById('goal-title').value,
                    topic: document.getElementById('goal-topic').value,
                    deadline: document.getElementById('goal-deadline').value,
                    description: document.getElementById('goal-description').value,
                    completed: document.getElementById('goal-completed').checked,
                    reminders: collectRemindersFromForm()
                };

                if (isEditing) {
                    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
                    fetch(`/api/objetivo/${id}`, { 
                        method: 'PUT', 
                        credentials: 'include',
                        headers: {
                            'Content-Type':'application/json',
                            'Accept': 'application/json',
                            'X-Requested-With': 'XMLHttpRequest',
                            'X-CSRF-TOKEN': csrfToken || ''
                        }, 
                        body: JSON.stringify(newGoalData) 
                    }).then(r => { if (r.ok) { closeModal(goalModal); loadAndRender(); } });
                } else {
                    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
                    fetch('/api/objetivo', { 
                        method: 'POST', 
                        credentials: 'include',
                        headers: {
                            'Content-Type':'application/json',
                            'Accept': 'application/json',
                            'X-Requested-With': 'XMLHttpRequest',
                            'X-CSRF-TOKEN': csrfToken || ''
                        }, 
                        body: JSON.stringify(newGoalData) 
                    }).then(r => { if (r.ok) { closeModal(goalModal); loadAndRender(); } });
                }
            });
            
            // --- Funções de Manipulação de Dados ---
            
            function toggleGoalCompletion(goalId, completed) {
                const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
                fetch(`/api/objetivo/${goalId}`, { 
                    method: 'PUT', 
                    credentials: 'include',
                    headers: {
                        'Content-Type':'application/json',
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest',
                        'X-CSRF-TOKEN': csrfToken || ''
                    }, 
                    body: JSON.stringify({ completed }) 
                }).then(r => { if (r.ok) loadAndRender(); });
            }

            function toggleReminderCompletion(goalId, reminderId, completed) {
                // Update reminder state by fetching the goal, modifying reminders and saving
                fetch(`/api/objetivo/${goalId}`).then(r => r.ok && r.json()).then(goal => {
                    if (!goal) return;
                    goal.reminders = (goal.reminders || []).map(r => r.id === reminderId ? { ...r, completed } : r);
                    fetch(`/api/objetivo/${goalId}`, { method: 'PUT', headers: {'Content-Type':'application/json'}, body: JSON.stringify({ reminders: goal.reminders }) })
                        .then(r => { if (r.ok) loadAndRender(); });
                });
            }
            
            // --- Inicialização ---
            loadAndRender();
        });
</script>
@endpush