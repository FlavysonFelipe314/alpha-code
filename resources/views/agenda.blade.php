@extends('layouts.app')

@section('title', 'Agenda Diária')

@push('styles')
<style>
        /* Cor de destaque: Vermelho (Tema Consistente) */
        .agenda-color {
            color: #EF4444; 
        }
        .agenda-bg-color {
            background-color: #EF4444; 
        }


        /* Estilo do Card de Evento */
        .event-card {
            background: rgba(16, 16, 16, 0.6);
            border: 1px solid #262626;
            backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(12px);
            transition: all 0.3s ease;
            position: relative;
        }
        .event-card:hover {
            border-left: 4px solid #EF4444; /* Barra lateral vermelha no hover */
            transform: scale(1.01);
            background: rgba(31, 41, 55, 0.5);
        }
        
        /* Estilo do Modal */
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
            border-color: #EF4444; 
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
            <i class="fas fa-calendar-alt text-3xl agenda-color"></i> 
            <h1 class="text-2xl font-black text-white uppercase tracking-wider">Agenda Diária</h1>
        </div>
        <div class="flex items-center space-x-3">
            <button id="add-event-btn" class="primary-action-btn font-bold py-2 px-4 rounded-lg text-sm transition-transform hover:scale-105 flex items-center justify-center space-x-2">
                <i class="fas fa-plus"></i>
                <span>Novo Evento</span>
            </button>
        </div>
    </header>

    <!-- Conteúdo Principal -->
    <main class="container mx-auto p-4 md:p-6 lg:p-8 pt-0">
        
        <!-- Controles de Data e Navegação -->
        <div class="flex flex-col md:flex-row justify-between items-center mb-8 space-y-4 md:space-y-0">
            <div class="flex items-center space-x-3">
                <button id="prev-day-btn" class="p-3 bg-neutral-800 hover:bg-neutral-700 rounded-lg text-white transition"><i class="fas fa-chevron-left"></i></button>
                <input type="date" id="date-selector" class="form-input text-lg font-bold text-center cursor-pointer" required>
                <button id="next-day-btn" class="p-3 bg-neutral-800 hover:bg-neutral-700 rounded-lg text-white transition"><i class="fas fa-chevron-right"></i></button>
            </div>
            <p id="current-day-label" class="text-xl font-bold text-white"></p>
        </div>
        
        <!-- Área da Agenda (Visualização de Lista por Horário) -->
        <section id="agenda-list-container" class="space-y-4 max-w-4xl mx-auto">
            <!-- Linha de tempo vertical (opcional, mas visualmente impactante) -->
            <div class="relative pl-6">
                <!-- Linha vertical decorativa -->
                <div class="absolute left-0 top-0 bottom-0 w-1 bg-neutral-800 rounded-full"></div>
                
                <div id="events-timeline" class="space-y-6">
                    <!-- Os eventos serão inseridos aqui pelo JS -->
                </div>
                
                <div id="empty-day-message" class="hidden text-center text-neutral-600 border-2 border-dashed border-neutral-800 rounded-lg p-12 mt-10">
                    <i class="fas fa-calendar-check fa-3x agenda-color mb-3"></i>
                    <p class="font-bold text-lg text-white">Dia Livre!</p>
                    <p class="text-sm mt-1">Nenhum evento agendado para esta data. Clique em "Novo Evento" para começar a planejar.</p>
                </div>
            </div>
        </section>
        
</div>

    <!-- --- MODAL PARA ADICIONAR/EDITAR EVENTO --- -->
    <div id="event-modal" class="modal"> 
        <div class="modal-content max-h-[90vh] flex flex-col"> 
            <button class="close-button" data-modal-id="event-modal">&times;</button> 
            
            <h2 id="event-modal-title" class="text-2xl font-bold text-white mb-6 flex-shrink-0">Novo Evento</h2> 
            
            <form id="event-form" class="flex-grow overflow-y-auto pr-4 space-y-4"> 
                <input type="hidden" id="event-id"> 
                
                <div>
                    <label for="event-title" class="block text-sm font-medium text-neutral-300 mb-1">Título do Evento</label>
                    <input type="text" id="event-title" placeholder="Ex: Reunião de Equipe, Consulta Médica" class="mt-1 w-full form-input" required>
                </div> 
                
                <div class="grid grid-cols-2 gap-4"> 
                    <div> 
                        <label for="event-date" class="block text-sm font-medium text-neutral-300 mb-1">Data</label> 
                        <input type="date" id="event-date" class="mt-1 w-full form-input" required>
                    </div> 
                    <div> 
                        <label for="event-time" class="block text-sm font-medium text-neutral-300 mb-1">Hora</label> 
                        <input type="time" id="event-time" class="mt-1 w-full form-input" required>
                    </div> 
                </div> 
                
                <div class="grid grid-cols-2 gap-4"> 
                    <div> 
                        <label for="event-duration" class="block text-sm font-medium text-neutral-300 mb-1">Duração (min)</label> 
                        <input type="number" step="5" min="5" id="event-duration" placeholder="60" class="mt-1 w-full form-input">
                    </div> 
                    <div> 
                        <label for="event-category" class="block text-sm font-medium text-neutral-300 mb-1">Categoria</label> 
                        <select id="event-category" class="mt-1 w-full form-select"> 
                            <option value="trabalho">Trabalho</option>
                            <option value="pessoal">Pessoal</option>
                            <option value="saude">Saúde</option>
                            <option value="estudo">Estudo</option>
                            <option value="lazer">Lazer</option>
                        </select> 
                    </div> 
                </div> 

                <div>
                    <label for="event-notes" class="block text-sm font-medium text-neutral-300 mb-1">Notas</label>
                    <textarea id="event-notes" rows="2" class="mt-1 w-full form-input" placeholder="Detalhes importantes..."></textarea>
                </div> 

                <div class="flex items-center pt-2">
                    <input type="checkbox" id="event-completed" class="h-4 w-4 rounded border-gray-300 text-red-600 focus:ring-red-500 bg-neutral-700 border-neutral-600">
                    <label for="event-completed" class="ml-2 block text-sm text-neutral-300">Marcar como Concluído</label>
                </div>
                
                <!-- Botão de Submissão -->
                <div class="pt-6 border-t border-neutral-700 flex-shrink-0">
                    <button type="submit" class="w-full primary-action-btn font-bold py-3 rounded-lg transition">Guardar Evento</button>
                </div> 
            </form> 
        </div> 
    </div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', () => {
        const API_BASE_URL = `${window.location.origin}/api`;
            
        // --- Seletores DOM ---
        const addEventBtn = document.getElementById('add-event-btn');
        const eventModal = document.getElementById('event-modal');
        const eventForm = document.getElementById('event-form');
        const dateSelector = document.getElementById('date-selector');
        const prevDayBtn = document.getElementById('prev-day-btn');
        const nextDayBtn = document.getElementById('next-day-btn');
        const currentDayLabel = document.getElementById('current-day-label');
        const eventsTimeline = document.getElementById('events-timeline');
        const emptyDayMessage = document.getElementById('empty-day-message');

        // --- Cache de eventos por data ---
        let eventsData = new Map();
        
        const today = new Date();
        const formatDateInput = (date) => date.toISOString().split('T')[0];
        const todayKey = formatDateInput(today);
        let selectedDate = todayKey;

        // --- Funções de API ---
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
                if (response.status === 204) return { status: true };
                return await response.json();
            } catch (error) {
                console.error('Erro na API:', error);
                return null;
            }
        }

        async function loadEventsForDate(dateKey, forceReload = false) {
            // Se já temos os eventos em cache e não forçou reload, retorna do cache
            if (!forceReload && eventsData.has(dateKey)) {
                return eventsData.get(dateKey);
            }
            
            const response = await fetchAPI(`/agenda?date=${dateKey}`);
            console.log('Resposta da API para', dateKey, ':', response);
            
            // A resposta da API vem como { status: true, data: [...] }
            let eventosArray = [];
            if (response && response.status && response.data) {
                eventosArray = Array.isArray(response.data) ? response.data : [];
            } else if (Array.isArray(response)) {
                eventosArray = response;
            }
            
            // Filtra eventos do dia atual para garantir
            const dateKeyStr = dateKey.split('T')[0];
            eventosArray = eventosArray.filter(evento => {
                if (!evento.date) return false;
                const eventDate = new Date(evento.date);
                const eventDateStr = eventDate.toISOString().split('T')[0];
                return eventDateStr === dateKeyStr;
            });
            
            console.log('Eventos filtrados para', dateKey, ':', eventosArray);
            eventsData.set(dateKey, eventosArray);
            return eventosArray;
        }

        async function saveEvent(eventData) {
            const isEditing = !!eventData.id;
            const endpoint = isEditing ? `/agenda/${eventData.id}` : '/agenda';
            const method = isEditing ? 'PUT' : 'POST';
            
            const payload = {
                title: eventData.title,
                date: eventData.date,
                time: eventData.time,
                duration: eventData.duration || null,
                category: eventData.category || null,
                notes: eventData.notes || null,
                completed: eventData.completed || false
            };
            
            const result = await fetchAPI(endpoint, {
                method,
                body: JSON.stringify(payload)
            });
            
            if (result) {
                // Limpa o cache da data do evento para forçar recarregar
                eventsData.delete(eventData.date);
                return result;
            }
            return null;
        }

        async function deleteEventFromAPI(eventId) {
            const result = await fetchAPI(`/agenda/${eventId}`, { method: 'DELETE' });
            if (result) {
                // Limpa todo o cache para forçar recarregar
                eventsData.clear();
                return true;
            }
            return false;
        }

            // --- Funções Auxiliares de Data e Hora ---
            
            function parseDate(dateKey) {
                // Cria um objeto Date seguro, assumindo fusos horários locais no início do dia
                return new Date(dateKey + 'T00:00:00'); 
            }

            function formatDisplayDate(dateKey) {
                const date = parseDate(dateKey);
                const options = { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' };
                return date.toLocaleDateString('pt-BR', options);
            }

            function formatTime(timeString) {
                // Se a hora for 'HH:MM', retorna apenas 'HH:MM'
                if (timeString.length === 5) return timeString;
                // Caso contrário (se for um objeto Date), formata
                const date = new Date(`2000-01-01T${timeString}`);
                return date.toLocaleTimeString('pt-BR', { hour: '2-digit', minute: '2-digit' });
            }
            
            // --- Funções do Modal ---

            function openModal(modal) { modal.classList.add('active'); }
            function closeModal(modal) { 
                modal.classList.remove('active');
                eventForm.reset(); 
            }
            
            // --- Funções de Renderização de UI ---

            function getIconForCategory(category) {
                if (!category) return 'fas fa-clock';
                switch(category.toLowerCase()) {
                    case 'trabalho': return 'fas fa-briefcase';
                    case 'pessoal': return 'fas fa-user';
                    case 'saude':
                    case 'saúde': return 'fas fa-heartbeat';
                    case 'estudo': return 'fas fa-book-reader';
                    case 'lazer': return 'fas fa-gamepad';
                    default: return 'fas fa-clock';
                }
            }

            async function renderEventsTimeline(dateKey, forceReload = false) {
                const dayEvents = await loadEventsForDate(dateKey, forceReload);
                eventsTimeline.innerHTML = '';

                if (dayEvents.length === 0) {
                    eventsTimeline.classList.add('hidden');
                    emptyDayMessage.classList.remove('hidden');
                    return;
                }

                eventsTimeline.classList.remove('hidden');
                emptyDayMessage.classList.add('hidden');

                // Ordena os eventos por hora
                dayEvents.sort((a, b) => a.time.localeCompare(b.time));

                dayEvents.forEach(event => {
                    const iconClass = getIconForCategory(event.category);
                    const completedClass = event.completed ? 'opacity-60 line-through' : '';
                    const completedIcon = event.completed ? 'fa-check-circle text-green-500' : 'fa-circle text-neutral-500';
                    
                    // Calculo do horário final (opcional)
                    const [hour, minute] = event.time.split(':').map(Number);
                    const endTime = new Date();
                    endTime.setHours(hour, minute + (event.duration || 0), 0);
                    const endTimeStr = event.duration ? ` - ${formatTime(endTime.toTimeString())}` : '';


                    const eventItem = document.createElement('div');
                    eventItem.className = "relative ml-4 p-4 event-card rounded-lg border-l-4 border-red-600";
                    eventItem.innerHTML = `
                        <!-- Ponto na linha do tempo -->
                        <div class="absolute left-[-29px] top-4 w-5 h-5 agenda-bg-color rounded-full border-4 border-neutral-900 z-10"></div>
                        
                        <div class="flex justify-between items-start">
                            <div class="flex-grow ${completedClass}">
                                <h3 class="text-lg font-bold text-white mb-1 flex items-center space-x-2">
                                    <i class="${iconClass} agenda-color"></i>
                                    <span>${event.title}</span>
                                </h3>
                                <p class="text-sm text-neutral-400 font-semibold mb-2">
                                    ${formatTime(event.time)}${endTimeStr} 
                                    ${event.category ? `<span class="ml-2 text-xs uppercase bg-neutral-700/50 px-2 py-0.5 rounded">${event.category.toUpperCase()}</span>` : ''}
                                </p>
                                ${event.notes ? `<p class="text-sm text-neutral-300 mt-1">${event.notes}</p>` : ''}
                            </div>
                            <div class="flex space-x-2 flex-shrink-0">
                                <button class="toggle-complete-btn h-8 w-8 rounded-lg text-neutral-400 hover:bg-neutral-700 transition" data-id="${event.id}" title="Marcar como ${event.completed ? 'Incompleto' : 'Completo'}">
                                    <i class="fas ${completedIcon} fa-sm"></i>
                                </button>
                                <button class="edit-event-btn h-8 w-8 rounded-lg text-neutral-400 hover:bg-neutral-700 hover:text-white transition" data-id="${event.id}" title="Editar">
                                    <i class="fas fa-pencil-alt fa-xs"></i>
                                </button>
                                <button class="delete-event-btn h-8 w-8 rounded-lg text-neutral-400 hover:bg-neutral-700 hover:text-red-400 transition" data-id="${event.id}" title="Eliminar">
                                    <i class="fas fa-trash-alt fa-xs"></i>
                                </button>
                            </div>
                        </div>
                    `;
                    eventsTimeline.appendChild(eventItem);
                });
            }

            async function updateUI(dateKey, forceReload = false) {
                dateSelector.value = dateKey;
                currentDayLabel.textContent = formatDisplayDate(dateKey);
                selectedDate = dateKey;
                await renderEventsTimeline(dateKey, forceReload);
            }

            // --- Lógica de Eventos e Formulário ---
            
            // Navegação de Data
            dateSelector.addEventListener('change', async (e) => {
                await updateUI(e.target.value);
            });

            prevDayBtn.addEventListener('click', async () => {
                const current = parseDate(selectedDate);
                current.setDate(current.getDate() - 1);
                await updateUI(formatDateInput(current));
            });

            nextDayBtn.addEventListener('click', async () => {
                const current = parseDate(selectedDate);
                current.setDate(current.getDate() + 1);
                await updateUI(formatDateInput(current));
            });

            // Modal de Evento
            addEventBtn.addEventListener('click', () => {
                document.getElementById('event-modal-title').textContent = 'Novo Evento';
                document.getElementById('event-id').value = '';
                document.getElementById('event-date').value = selectedDate; // Preenche com a data atual
                document.getElementById('event-completed').checked = false;
                openModal(eventModal);
            });

            eventModal.querySelector('.close-button').addEventListener('click', () => closeModal(eventModal));

            eventForm.addEventListener('submit', async (e) => {
                e.preventDefault();
                
                const id = document.getElementById('event-id').value;
                const isEditing = !!id;
                
                const newEventData = {
                    id: isEditing ? parseInt(id) : null,
                    title: document.getElementById('event-title').value,
                    date: document.getElementById('event-date').value,
                    time: document.getElementById('event-time').value,
                    duration: parseInt(document.getElementById('event-duration').value) || null,
                    category: document.getElementById('event-category').value || null,
                    notes: document.getElementById('event-notes').value || null,
                    completed: document.getElementById('event-completed').checked || false
                };

                const result = await saveEvent(newEventData);
                
                if (result) {
                    closeModal(eventModal);
                    const eventKey = newEventData.date;
                    
                    // Atualiza a visualização se a data editada/criada for a data selecionada
                    if (eventKey === selectedDate) {
                        await updateUI(eventKey, true);
                    } else {
                        // Se o evento foi adicionado em outro dia, apenas atualiza a data selecionada
                        await updateUI(selectedDate, true); 
                    }
                } else {
                    alert('Erro ao salvar evento. Tente novamente.');
                }
            });
            
            // Ações na Timeline (Editar, Eliminar, Concluir)
            eventsTimeline.addEventListener('click', async (e) => {
                const editBtn = e.target.closest('.edit-event-btn');
                const deleteBtn = e.target.closest('.delete-event-btn');
                const toggleBtn = e.target.closest('.toggle-complete-btn');
                
                if (editBtn) {
                    const eventId = parseInt(editBtn.dataset.id);
                    const dayEvents = await loadEventsForDate(selectedDate);
                    const event = dayEvents.find(ev => ev.id === eventId);
                    if (event) {
                        await openEditEventModal(event);
                    }
                } else if (deleteBtn) {
                    const eventId = parseInt(deleteBtn.dataset.id);
                    if (confirm('Tem certeza que deseja eliminar este evento?')) {
                        await deleteEvent(selectedDate, eventId);
                    }
                } else if (toggleBtn) {
                    const eventId = parseInt(toggleBtn.dataset.id);
                    await toggleEventCompletion(selectedDate, eventId);
                }
            });

            async function openEditEventModal(event) {
                document.getElementById('event-modal-title').textContent = 'Editar Evento';
                document.getElementById('event-id').value = event.id;
                document.getElementById('event-title').value = event.title;
                document.getElementById('event-date').value = event.date;
                document.getElementById('event-time').value = event.time;
                document.getElementById('event-duration').value = event.duration || '';
                document.getElementById('event-category').value = event.category || '';
                document.getElementById('event-notes').value = event.notes || '';
                document.getElementById('event-completed').checked = event.completed || false;
                openModal(eventModal);
            }

            async function deleteEvent(dateKey, eventId) {
                const success = await deleteEventFromAPI(eventId);
                if (success) {
                    await updateUI(dateKey, true);
                }
            }

            async function toggleEventCompletion(dateKey, eventId) {
                const dayEvents = await loadEventsForDate(dateKey);
                const event = dayEvents.find(ev => ev.id === eventId);
                if (event) {
                    const updatedEvent = { ...event, completed: !event.completed };
                    const result = await saveEvent(updatedEvent);
                    if (result) {
                        await updateUI(dateKey, true);
                    }
                }
            }


            // --- Inicialização ---
            (async () => {
                await updateUI(todayKey);
                dateSelector.valueAsDate = today;
            })();
        });
</script>
@endpush