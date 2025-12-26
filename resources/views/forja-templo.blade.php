@extends('layouts.app')

@section('title', 'Treino')

@push('styles')
<style>
    .modal {
        position: fixed; top: 0; left: 0; width: 100%; height: 100%;
        display: flex; align-items: center; justify-content: center;
        background-color: rgba(0, 0, 0, 0.8); backdrop-filter: blur(8px);
        z-index: 1000; opacity: 0; visibility: hidden; transition: opacity 0.3s, visibility 0.3s;
    }
    .modal.active { opacity: 1; visibility: visible; }
    .modal-content {
        background: #18181b; border: 1px solid #3f3f46; border-radius: 12px;
        padding: 2rem; width: 90%; max-width: 600px;
        transform: scale(0.95); transition: transform 0.3s; position: relative;
        max-height: 90vh; overflow-y: auto;
    }
    .modal.active .modal-content { transform: scale(1); }
    .close-button {
        position: absolute; top: 1rem; right: 1rem; background: none; border: none;
        color: #71717a; font-size: 1.5rem; cursor: pointer; transition: color 0.3s;
    }
    .close-button:hover { color: white; }
    .day-tab.active {
        background-color: #ef4444;
        color: white;
        box-shadow: 0 0 15px rgba(239, 68, 68, 0.5);
    }
    .workout-card {
        background: rgba(16, 16, 16, 0.6);
        border: 1px solid #262626;
        backdrop-filter: blur(12px);
        -webkit-backdrop-filter: blur(12px);
        transition: all 0.3s ease;
    }
    .workout-card:hover {
        transform: translateY(-5px);
        border-color: rgba(239, 68, 68, 0.5);
    }
    .form-input {
        background-color: #27272a;
        border: 1px solid #3f3f46;
        border-radius: 0.5rem;
        padding: 0.75rem 1rem;
        color: white;
        transition: border-color 0.2s, box-shadow 0.2s;
    }
    .form-input:focus {
        outline: none;
        border-color: #ef4444;
        box-shadow: 0 0 0 2px rgba(239, 68, 68, 0.5);
    }
    .day-select-button {
        border: 1px solid #3f3f46;
        color: #a1a1aa;
    }
    .day-select-button.active {
        background-color: #ef4444;
        border-color: #ef4444;
        color: white;
    }
    .radio-card {
        background-color: #27272a;
        border: 1px solid #3f3f46;
        transition: all 0.2s;
    }
    .radio-card:hover {
        border-color: #71717a;
    }
    .radio-card.active {
        border-color: #ef4444;
        box-shadow: 0 0 0 2px rgba(239, 68, 68, 0.5);
        background-color: rgba(239, 68, 68, 0.1);
    }
</style>
@endpush

@section('content')
<div class="container mx-auto p-4 md:p-6 lg:p-8">
    <header class="flex flex-col md:flex-row justify-between items-center space-y-4 md:space-y-0 pb-6">
        <div class="flex items-center space-x-4">
            <h1 class="text-2xl font-black text-white uppercase tracking-wider">treino.ai</h1>
        </div>
        <div class="flex items-center space-x-3">
            <button id="add-treino-btn" class="bg-neutral-800 border border-neutral-700 text-neutral-300 hover:bg-neutral-700 font-semibold py-2 px-4 rounded-lg text-sm transition-transform hover:scale-105 flex items-center justify-center space-x-2">
                <i class="fas fa-plus fa-xs"></i>
                <span>Manual</span>
            </button>
            <button id="open-ia-modal-btn" class="bg-red-600 hover:bg-red-700 text-white font-bold py-2 px-4 rounded-lg text-sm transition-transform hover:scale-105 flex items-center justify-center space-x-2">
                <i class="fas fa-magic-sparkles"></i>
                <span>Gerar com I.A.</span>
            </button>
        </div>
    </header>

    <nav id="week-days-nav" class="flex justify-center space-x-1 md:space-x-2 my-8 p-1 bg-neutral-900/50 rounded-full border border-neutral-800"></nav>
    <div id="planner-container"></div>
    <div id="loader" class="col-span-full text-center text-neutral-500 mt-20"></div>

    <!-- Modal para Adicionar/Editar Treino -->
    <div id="treino-modal" class="modal">
        <div class="modal-content max-h-[90vh] flex flex-col">
            <button class="close-button">&times;</button>
            <h2 id="treino-modal-title" class="text-2xl font-bold text-white mb-6 flex-shrink-0">Novo Treino</h2>
            <form id="treino-form" class="flex-grow overflow-y-auto pr-4 space-y-6">
                <input type="hidden" id="treino-id-input">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <input type="text" id="treino-name-input" placeholder="Nome do Treino (Ex: Peito e Tríceps)" class="form-input" required>
                    <input type="time" id="treino-time-input" class="form-input" required>
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-neutral-300 mb-2">Dia da Semana</label>
                    <div id="treino-day-buttons" class="flex flex-wrap gap-2"></div>
                </div>

                <div>
                    <h3 class="font-bold text-white mb-2">Exercícios</h3>
                    <div id="exercicios-list" class="space-y-3"></div>
                    <button type="button" id="add-exercicio-btn" class="mt-2 text-sm text-red-400 hover:text-red-300 transition"><i class="fas fa-plus mr-2"></i>Adicionar Exercício</button>
                </div>
                
                <div>
                    <label for="treino-observation-input" class="block text-sm font-medium text-neutral-300">Observações</label>
                    <textarea id="treino-observation-input" placeholder="Ex: Descansar 60-90s entre séries" rows="2" class="mt-1 w-full form-input"></textarea>
                </div>

                <div class="pt-6 border-t border-neutral-700 flex-shrink-0">
                    <button type="submit" class="w-full bg-red-600 hover:bg-red-700 text-white font-bold py-3 rounded-lg transition">Guardar Treino</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal para Geração com I.A. -->
    <div id="ia-modal" class="modal">
        <div class="modal-content">
            <button class="close-button">&times;</button>
            <h2 class="text-2xl font-bold text-white mb-2">Gerar Plano com I.A.</h2>
            <p class="text-sm text-neutral-400 mb-6">Responda a algumas perguntas para criar o seu plano personalizado.</p>
            
            <div class="w-full bg-neutral-700 rounded-full h-2 mb-6">
                <div id="ia-progress-bar" class="bg-red-600 h-2 rounded-full transition-all duration-500" style="width: 33.3%"></div>
            </div>

            <form id="ia-form">
                <div id="ia-step-1" class="ia-step space-y-4">
                    <h3 class="font-bold text-white text-lg">Passo 1: Sobre Si</h3>
                    <div>
                        <label class="block text-sm font-medium text-neutral-300 mb-2">Nível de Experiência</label>
                        <select id="ia-experiencia" class="w-full form-input" required>
                            <option value="">Selecione...</option>
                            <option value="iniciante">Iniciante (0-6 meses)</option>
                            <option value="intermediario">Intermediário (6 meses - 2 anos)</option>
                            <option value="avancado">Avançado (2+ anos)</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-neutral-300 mb-2">Dias Disponíveis por Semana</label>
                        <select id="ia-dias-semana" class="w-full form-input" required>
                            <option value="">Selecione...</option>
                            <option value="3">3 dias</option>
                            <option value="4">4 dias</option>
                            <option value="5">5 dias</option>
                            <option value="6">6 dias</option>
                            <option value="7">7 dias</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-neutral-300 mb-2">Equipamentos Disponíveis</label>
                        <select id="ia-equipamentos" class="w-full form-input" required>
                            <option value="">Selecione...</option>
                            <option value="academia completa">Academia Completa</option>
                            <option value="casa">Casa (peso corporal e equipamentos básicos)</option>
                            <option value="peso corporal">Apenas Peso Corporal</option>
                        </select>
                    </div>
                </div>

                <div id="ia-step-2" class="ia-step hidden space-y-4">
                    <h3 class="font-bold text-white text-lg">Passo 2: O Seu Objetivo</h3>
                    <div class="grid grid-cols-2 gap-4" id="ia-objetivos-cards">
                        <div class="radio-card p-4 rounded-lg cursor-pointer text-center active" data-objetivo="hipertrofia">
                            <i class="fas fa-dumbbell text-red-500 text-2xl mb-2"></i>
                            <p>Hipertrofia</p>
                        </div>
                        <div class="radio-card p-4 rounded-lg cursor-pointer text-center" data-objetivo="forca">
                            <i class="fas fa-fire text-red-500 text-2xl mb-2"></i>
                            <p>Força</p>
                        </div>
                        <div class="radio-card p-4 rounded-lg cursor-pointer text-center" data-objetivo="resistencia">
                            <i class="fas fa-running text-red-500 text-2xl mb-2"></i>
                            <p>Resistência</p>
                        </div>
                        <div class="radio-card p-4 rounded-lg cursor-pointer text-center" data-objetivo="definicao">
                            <i class="fas fa-heart-pulse text-red-500 text-2xl mb-2"></i>
                            <p>Definição</p>
                        </div>
                    </div>
                </div>

                <div id="ia-step-3" class="ia-step hidden space-y-4">
                    <h3 class="font-bold text-white text-lg">Passo 3: Informações Adicionais</h3>
                    <textarea id="ia-lesoes" rows="3" class="w-full form-input" placeholder="Limitações físicas ou lesões (deixe em branco se não tiver)..."></textarea>
                    <textarea id="ia-preferencias" rows="2" class="w-full form-input" placeholder="Preferências de treino ou exercícios favoritos..."></textarea>
                </div>

                <div class="mt-8 pt-6 border-t border-neutral-700 flex justify-between items-center">
                    <button type="button" id="ia-prev-btn" class="bg-neutral-700 hover:bg-neutral-600 text-white font-bold py-2 px-5 rounded-lg transition invisible">Anterior</button>
                    <div class="flex-grow flex justify-end space-x-2">
                        <button type="button" id="ia-next-btn" class="bg-red-600 hover:bg-red-700 text-white font-bold py-2 px-5 rounded-lg transition">Próximo</button>
                        <button type="submit" id="ia-submit-btn" class="bg-red-600 hover:bg-red-700 text-white font-bold py-2 px-5 rounded-lg transition hidden">
                            <i class="fas fa-magic-sparkles mr-2"></i>Gerar Plano
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
    
    <!-- Modal de Loading -->
    <div id="loading-modal" class="modal">
        <div class="text-center text-white">
            <i class="fas fa-spinner fa-spin fa-3x text-red-500"></i>
            <p class="mt-4 font-bold text-lg">A I.A. está a criar o seu plano...</p>
            <p class="text-sm text-neutral-400">Isto pode demorar alguns momentos.</p>
        </div>
    </div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', () => {
        const API_BASE_URL = `${window.location.origin}/api`;
        
        const loader = document.getElementById('loader');
        const addTreinoBtn = document.getElementById('add-treino-btn');
        const openIaModalBtn = document.getElementById('open-ia-modal-btn');
        const treinoModal = document.getElementById('treino-modal');
        const iaModal = document.getElementById('ia-modal');
        const loadingModal = document.getElementById('loading-modal');
        const treinoForm = document.getElementById('treino-form');
        const treinoModalTitle = document.getElementById('treino-modal-title');
        const treinoIdInput = document.getElementById('treino-id-input');
        const treinoNameInput = document.getElementById('treino-name-input');
        const treinoTimeInput = document.getElementById('treino-time-input');
        const treinoDayButtons = document.getElementById('treino-day-buttons');
        const treinoObservationInput = document.getElementById('treino-observation-input');
        const exerciciosList = document.getElementById('exercicios-list');
        const addExercicioBtn = document.getElementById('add-exercicio-btn');
        const weekDaysNav = document.getElementById('week-days-nav');
        const plannerContainer = document.getElementById('planner-container');
        const iaForm = document.getElementById('ia-form');
        const iaProgressBar = document.getElementById('ia-progress-bar');
        const iaSteps = document.querySelectorAll('.ia-step');
        const iaPrevBtn = document.getElementById('ia-prev-btn');
        const iaNextBtn = document.getElementById('ia-next-btn');
        const iaSubmitBtn = document.getElementById('ia-submit-btn');
        const iaObjetivosCards = document.getElementById('ia-objetivos-cards');
        let currentIaStep = 1;

        const weekDays = [
            { id: 'segunda-feira', short: 'SEG', long: 'Segunda-Feira' },
            { id: 'terca-feira', short: 'TER', long: 'Terça-Feira' },
            { id: 'quarta-feira', short: 'QUA', long: 'Quarta-Feira' },
            { id: 'quinta-feira', short: 'QUI', long: 'Quinta-Feira' },
            { id: 'sexta-feira', short: 'SEX', long: 'Sexta-Feira' },
            { id: 'sabado', short: 'SAB', long: 'Sábado' },
            { id: 'domingo', short: 'DOM', long: 'Domingo' }
        ];

        function initializeUI() {
            weekDays.forEach(day => {
                weekDaysNav.innerHTML += `<button class="day-tab flex-1 px-2 py-2 text-xs md:px-4 md:text-sm font-bold text-neutral-400 rounded-full transition" data-day-id="${day.id}">${day.short}</button>`;
                plannerContainer.innerHTML += `<div id="day-column-${day.id}" class="day-column hidden"><h2 class="text-xl font-bold text-white mb-4">${day.long}</h2><div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 workout-cards-container"></div></div>`;
                treinoDayButtons.innerHTML += `<button type="button" class="day-select-button px-3 py-1 text-sm rounded-full" data-day-short="${day.short}">${day.short}</button>`;
            });
            const todayIndex = new Date().getDay() - 1;
            const currentDayId = weekDays[todayIndex < 0 ? 6 : todayIndex].id;
            switchDay(currentDayId);
        }

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
                if (!response.ok) {
                    const errorData = await response.json().catch(() => ({ message: response.statusText }));
                    let errorMessages = errorData.message || response.statusText;
                    if(errorData.errors) {
                        errorMessages += "\n" + Object.values(errorData.errors).flat().join("\n");
                    }
                    if(errorData.error) {
                        errorMessages += "\n" + errorData.error;
                    }
                    console.error('Erro detalhado da API:', errorData);
                    throw new Error(`Erro na API (${response.status}): ${errorMessages}`);
                }
                if (response.status === 204 || (response.status === 200 && options.method === 'DELETE')) return { status: true };
                return await response.json();
            } catch (error) {
                console.error(`Fetch error for ${endpoint}:`, error);
                alert(`Erro de comunicação com o servidor:\n${error.message}`);
                return null;
            }
        }
        
        function renderTreinos(treinos) {
            document.querySelectorAll('.workout-cards-container').forEach(c => c.innerHTML = '');

            treinos.forEach(treino => {
                const dayObject = weekDays.find(d => d.short === treino.day);
                if (!dayObject) return;
                
                const dayColumn = document.getElementById(`day-column-${dayObject.id}`);
                if (!dayColumn) return;

                const container = dayColumn.querySelector('.workout-cards-container');
                let exerciciosHtml = '';
                let notesHtml = '';
                
                // Tenta extrair exercícios e notas do campo observacoes
                try {
                    let exercicios = [];
                    let notes = '';
                    
                    if (treino.exercicios && Array.isArray(treino.exercicios)) {
                        // Se exercicios já está como array
                        exercicios = treino.exercicios;
                    } else if (treino.observacoes) {
                        // Tenta parsear o JSON do campo observacoes
                        try {
                            const parsed = JSON.parse(treino.observacoes);
                            if (parsed.exercicios && Array.isArray(parsed.exercicios)) {
                                exercicios = parsed.exercicios;
                            }
                            if (parsed.notes) {
                                notes = parsed.notes;
                            } else if (typeof parsed === 'string' && !parsed.exercicios) {
                                // Se não for JSON válido, usa como texto simples
                                notes = treino.observacoes;
                            }
                        } catch (e) {
                            // Se não for JSON, usa como texto simples
                            notes = treino.observacoes;
                        }
                    }
                    
                    // Formata os exercícios
                    if (exercicios.length > 0) {
                        exerciciosHtml = exercicios.map(e => {
                            const nome = e.nome || e.name || 'Exercício';
                            const series = e.series || '-';
                            const repeticoes = e.repeticoes || e.repeticoes || '-';
                            const carga = e.carga ? ` (${e.carga})` : '';
                            return `<li class="text-xs text-neutral-400">${nome} - ${series}×${repeticoes}${carga}</li>`;
                        }).join('');
                    } else {
                        exerciciosHtml = '<li class="text-xs text-neutral-500 italic">Nenhum exercício cadastrado</li>';
                    }
                    
                    // Formata as notas
                    if (notes) {
                        notesHtml = `<p class="text-xs italic text-neutral-500 mt-3 border-t border-neutral-700 pt-2">${notes}</p>`;
                    }
                } catch (e) {
                    console.error('Erro ao processar treino:', e, treino);
                    exerciciosHtml = '<li class="text-xs text-neutral-500 italic">Erro ao carregar exercícios</li>';
                }

                const card = document.createElement('div');
                card.className = "workout-card p-4 rounded-lg flex flex-col";
                card.innerHTML = `
                    <div class="flex-grow">
                        <div class="flex justify-between items-start mb-3">
                            <div>
                                <p class="font-bold text-md text-white">${treino.nome}</p>
                                <p class="text-sm text-red-400 font-bold">${treino.horario ? (treino.horario.substring(0, 5)) : '-'}</p>
                            </div>
                            <div class="flex space-x-1">
                                <button class="edit-btn h-7 w-7 rounded-lg text-neutral-400 hover:bg-neutral-700 hover:text-white" data-id="${treino.id}"><i class="fas fa-pencil-alt fa-xs"></i></button>
                                <button class="delete-btn h-7 w-7 rounded-lg text-neutral-400 hover:bg-neutral-700 hover:text-red-400" data-id="${treino.id}"><i class="fas fa-trash-alt fa-xs"></i></button>
                            </div>
                        </div>
                        <ul class="space-y-1.5 list-disc list-inside marker:text-red-500 mt-2">${exerciciosHtml}</ul>
                        ${notesHtml}
                    </div>
                `;
                container.appendChild(card);
            });
            
            document.querySelectorAll('.workout-cards-container').forEach(c => {
                if (c.innerHTML === '') {
                    c.innerHTML = `<div class="border-2 border-dashed border-neutral-800 rounded-lg p-10 text-center text-neutral-600"><i class="fas fa-dumbbell fa-2x mb-2"></i><p>Nenhum treino para este dia.</p></div>`;
                }
            });
        }

        async function loadTreinos() {
            loader.innerHTML = `<div class="animate-pulse grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6"><div class="h-40 bg-neutral-800 rounded-lg"></div><div class="h-40 bg-neutral-800 rounded-lg hidden md:block"></div><div class="h-40 bg-neutral-800 rounded-lg hidden lg:block"></div></div>`;
            plannerContainer.style.display = 'none';
            const response = await fetchAPI('/treino');
            loader.innerHTML = '';
            plannerContainer.style.display = 'block';
            if (response?.data) {
                // Os exercícios serão extraídos na função renderTreinos
                renderTreinos(response.data);
            } else if(response) { 
                renderTreinos([]);
            }
        }

        function createExercicioInput(exercicio = {nome: '', series: '', repeticoes: '', carga: ''}) {
            const div = document.createElement('div');
            div.className = 'grid grid-cols-12 gap-2 item-row';
            div.innerHTML = `
                <input type="text" placeholder="Nome do exercício" value="${exercicio.nome || exercicio.name || ''}" class="col-span-5 form-input" data-field="nome" required>
                <input type="number" placeholder="Séries" value="${exercicio.series || ''}" class="col-span-2 form-input" data-field="series" required>
                <input type="text" placeholder="Repetições" value="${exercicio.repeticoes || exercicio.repeticoes || ''}" class="col-span-3 form-input" data-field="repeticoes" required>
                <input type="text" placeholder="Carga" value="${exercicio.carga || ''}" class="col-span-1 form-input" data-field="carga">
                <button type="button" class="col-span-1 remove-item-btn text-neutral-500 hover:text-red-400"><i class="fas fa-times"></i></button>
            `;
            return div;
        }

        function openModal(modal) { modal.classList.add('active'); }
        function closeModal(modal) { modal.classList.remove('active'); }

        function openTreinoModal(mode = 'add', treino = null) {
            treinoForm.reset();
            exerciciosList.innerHTML = '';
            treinoIdInput.value = '';

            document.querySelectorAll('.day-select-button').forEach(b => b.classList.remove('active'));

            if (mode === 'edit' && treino) {
                treinoModalTitle.textContent = 'Editar Treino';
                treinoIdInput.value = treino.id;
                treinoNameInput.value = treino.nome;
                treinoTimeInput.value = treino.horario ? treino.horario.substring(0, 5) : '';
                treinoObservationInput.value = '';
                
                let exercicios = [];
                try {
                    if (treino.observacoes) {
                        const parsed = JSON.parse(treino.observacoes);
                        if (parsed.exercicios) exercicios = parsed.exercicios;
                        else treinoObservationInput.value = treino.observacoes;
                    }
                } catch (e) {
                    treinoObservationInput.value = treino.observacoes || '';
                }
                
                if (exercicios.length > 0) {
                    exercicios.forEach(ex => exerciciosList.appendChild(createExercicioInput(ex)));
                } else {
                    exerciciosList.appendChild(createExercicioInput());
                }
                
                const dayBtn = treinoDayButtons.querySelector(`[data-day-short="${treino.day}"]`);
                if(dayBtn) dayBtn.classList.add('active');
            } else {
                treinoModalTitle.textContent = 'Novo Treino';
                exerciciosList.appendChild(createExercicioInput());
                const activeDay = weekDaysNav.querySelector('.active')?.dataset.dayId;
                const dayObject = weekDays.find(d => d.id === activeDay);
                if(dayObject) {
                    const dayBtn = treinoDayButtons.querySelector(`[data-day-short="${dayObject.short}"]`);
                    if(dayBtn) dayBtn.classList.add('active');
                }
            }
            openModal(treinoModal);
        }

        function switchDay(dayId) {
            document.querySelectorAll('.day-tab').forEach(tab => tab.classList.remove('active'));
            document.querySelectorAll('.day-column').forEach(col => col.classList.add('hidden'));
            
            const activeTab = weekDaysNav.querySelector(`[data-day-id="${dayId}"]`);
            const activeColumn = document.getElementById(`day-column-${dayId}`);

            if(activeTab) activeTab.classList.add('active');
            if(activeColumn) activeColumn.classList.remove('hidden');
        }

        function navigateIaSteps(direction) {
            const newStep = currentIaStep + direction;
            if (newStep < 1 || newStep > 3) return;
            currentIaStep = newStep;

            iaSteps.forEach(step => step.classList.add('hidden'));
            document.getElementById(`ia-step-${currentIaStep}`).classList.remove('hidden');

            iaProgressBar.style.width = `${currentIaStep * 33.3}%`;
            iaPrevBtn.classList.toggle('invisible', currentIaStep === 1);
            
            const isFinalStep = currentIaStep === 3;
            iaNextBtn.classList.toggle('hidden', isFinalStep);
            iaSubmitBtn.classList.toggle('hidden', !isFinalStep);
        }

        addExercicioBtn.addEventListener('click', () => exerciciosList.appendChild(createExercicioInput()));
        exerciciosList.addEventListener('click', e => { if (e.target.closest('.remove-item-btn')) e.target.closest('.item-row').remove(); });

        addTreinoBtn.addEventListener('click', () => openTreinoModal('add'));
        treinoModal.querySelector('.close-button').addEventListener('click', () => closeModal(treinoModal));
        openIaModalBtn.addEventListener('click', () => openModal(iaModal));
        iaModal.querySelector('.close-button').addEventListener('click', () => closeModal(iaModal));
        weekDaysNav.addEventListener('click', e => { if(e.target.matches('.day-tab')) switchDay(e.target.dataset.dayId); });

        treinoDayButtons.addEventListener('click', e => {
            const button = e.target.closest('.day-select-button');
            if (button) {
                treinoDayButtons.querySelectorAll('.day-select-button').forEach(b => b.classList.remove('active'));
                button.classList.add('active');
            }
        });

        iaObjetivosCards.addEventListener('click', e => {
            const card = e.target.closest('.radio-card');
            if (card) {
                iaObjetivosCards.querySelectorAll('.radio-card').forEach(c => c.classList.remove('active'));
                card.classList.add('active');
            }
        });

        iaNextBtn.addEventListener('click', () => navigateIaSteps(1));
        iaPrevBtn.addEventListener('click', () => navigateIaSteps(-1));

        iaForm.addEventListener('submit', async (e) => {
            e.preventDefault();
            openModal(loadingModal);

            const payload = {
                nivel_experiencia: document.getElementById('ia-experiencia').value,
                dias_disponiveis: parseInt(document.getElementById('ia-dias-semana').value),
                equipamentos: document.getElementById('ia-equipamentos').value,
                objetivo: iaObjetivosCards.querySelector('.active').dataset.objetivo,
                limitacoes: document.getElementById('ia-lesoes').value,
                preferencias: document.getElementById('ia-preferencias').value
            };

            const aiResponse = await fetchAPI('/treino/think-ai', { method: 'POST', body: JSON.stringify(payload) });
            
            console.log('Resposta da IA:', aiResponse);
            console.log('Tipo da resposta:', typeof aiResponse);
            
            let treinosArray = null;
            
            // Tenta parsear a resposta como JSON se for string
            if (typeof aiResponse === 'string') {
                try {
                    // Remove possíveis markdown ou texto antes/depois do JSON
                    const jsonMatch = aiResponse.match(/\[[\s\S]*\]/);
                    if (jsonMatch) {
                        treinosArray = JSON.parse(jsonMatch[0]);
                    } else {
                        treinosArray = JSON.parse(aiResponse);
                    }
                } catch (e) {
                    console.error("Erro ao parsear resposta da IA como string:", e);
                    console.error("Conteúdo da resposta:", aiResponse);
                }
            } else if (Array.isArray(aiResponse)) {
                treinosArray = aiResponse;
            } else if (aiResponse && aiResponse.response && typeof aiResponse.response === 'string') {
                // Se a resposta veio dentro de um objeto com chave 'response'
                try {
                    const jsonMatch = aiResponse.response.match(/\[[\s\S]*\]/);
                    if (jsonMatch) {
                        treinosArray = JSON.parse(jsonMatch[0]);
                    } else {
                        treinosArray = JSON.parse(aiResponse.response);
                    }
                } catch (e) {
                    console.error("Erro ao parsear resposta da IA:", e);
                }
            }
            
            if (treinosArray && Array.isArray(treinosArray) && treinosArray.length > 0) {
                console.log('Treinos a serem criados:', treinosArray);
                
                const creationPromises = treinosArray.map(async (treino) => {
                    const exercicios = treino.exercicios || [];
                    // Garante que horário está no formato correto (HH:MM)
                    let horario = treino.horario || '18:00';
                    if (horario.length > 5) {
                        horario = horario.substring(0, 5);
                    }
                    
                    const treinoData = {
                        nome: treino.nome,
                        day: treino.day,
                        horario: horario,
                        observacoes: JSON.stringify({ 
                            exercicios: exercicios, 
                            notes: treino.observacoes || '' 
                        })
                    };
                    
                    console.log('Salvando treino:', treinoData);
                    
                    const result = await fetchAPI('/treino', { 
                        method: 'POST', 
                        body: JSON.stringify(treinoData)
                    });
                    
                    console.log('Resultado do salvamento:', result);
                    return result;
                });
                
                const results = await Promise.all(creationPromises);
                console.log('Todos os resultados:', results);
                
                const successCount = results.filter(r => r && (r.status || r.treino || r.data)).length;
                
                if (successCount > 0) {
                    closeModal(iaModal);
                    await loadTreinos();
                    alert(`Plano de treino gerado com sucesso! ${successCount} treino(s) adicionado(s).`);
                } else {
                    alert('Erro ao salvar os treinos. Verifique o console para mais detalhes.');
                }
            } else {
                console.error("Resposta da IA inválida ou vazia:", aiResponse);
                alert('A I.A. não retornou um plano válido. Tente novamente.');
            }
            
            closeModal(loadingModal);
        });

        plannerContainer.addEventListener('click', async (e) => {
            const editBtn = e.target.closest('.edit-btn');
            const deleteBtn = e.target.closest('.delete-btn');
            
            if (editBtn) {
                const treinoId = editBtn.dataset.id;
                const response = await fetchAPI(`/treino/${treinoId}`);
                if(response?.data) openTreinoModal('edit', response.data);
            }

            if (deleteBtn) {
                const treinoId = deleteBtn.dataset.id;
                if (confirm('Tem a certeza que quer eliminar este treino?')) {
                    const response = await fetchAPI(`/treino/${treinoId}`, { method: 'DELETE' });
                    if (response?.status) await loadTreinos();
                }
            }
        });

        treinoForm.addEventListener('submit', async (e) => {
            e.preventDefault();
            const id = treinoIdInput.value;
            const isEditing = !!id;

            const getExerciciosFromList = () => Array.from(exerciciosList.querySelectorAll('.item-row')).map(row => ({
                nome: row.querySelector('[data-field="nome"]').value,
                series: row.querySelector('[data-field="series"]').value,
                repeticoes: row.querySelector('[data-field="repeticoes"]').value,
                carga: row.querySelector('[data-field="carga"]').value || null,
            }));
            
            const activeDayBtn = treinoDayButtons.querySelector('.active');
            if (!activeDayBtn) {
                alert('Por favor, selecione um dia da semana.');
                return;
            }

            const exercicios = getExerciciosFromList();
            const observacoesData = {
                exercicios: exercicios,
                notes: treinoObservationInput.value
            };

            const data = {
                nome: treinoNameInput.value,
                horario: treinoTimeInput.value + ':00',
                day: activeDayBtn.dataset.dayShort,
                observacoes: JSON.stringify(observacoesData),
            };

            const endpoint = isEditing ? `/treino/${id}` : '/treino';
            const method = isEditing ? 'PUT' : 'POST';

            const response = await fetchAPI(endpoint, { method, body: JSON.stringify(data) });

            if (response?.status || response?.treino || response?.data) {
                closeModal(treinoModal);
                await loadTreinos();
            }
        });

        initializeUI();
        loadTreinos();
    });
</script>
@endpush
