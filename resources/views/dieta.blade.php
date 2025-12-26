@extends('layouts.app')

@section('title', 'Plano Alimentar')

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
        .meal-card {
            background: rgba(16, 16, 16, 0.6);
            border: 1px solid #262626;
            backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(12px);
            transition: all 0.3s ease;
        }
        .meal-card:hover {
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
                {{-- <img src="{{ @asset('Assets/logo.png') }}" alt="Logo" class="h-10 w-auto" style="    object-fit: cover;width: 100px;"> --}}
                <h1 class="text-2xl font-black text-white uppercase tracking-wider">nutri.ai</h1>
            </div>
            <div class="flex items-center space-x-3">
                <button id="add-dieta-btn" class="bg-neutral-800 border border-neutral-700 text-neutral-300 hover:bg-neutral-700 font-semibold py-2 px-4 rounded-lg text-sm transition-transform hover:scale-105 flex items-center justify-center space-x-2">
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

    <!-- Modal para Adicionar/Editar Dieta -->
    <div id="dieta-modal" class="modal">
        <div class="modal-content max-h-[90vh] flex flex-col">
            <button class="close-button">&times;</button>
            <h2 id="dieta-modal-title" class="text-2xl font-bold text-white mb-6 flex-shrink-0">Nova Refeição</h2>
            <form id="dieta-form" class="flex-grow overflow-y-auto pr-4 space-y-6">
                <input type="hidden" id="dieta-id-input">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <input type="text" id="dieta-name-input" placeholder="Nome da Refeição (Ex: Café da Manhã)" class="form-input" required>
                    <input type="time" id="dieta-time-input" class="form-input" required>
                </div>
                
                <div>
                     <label class="block text-sm font-medium text-neutral-300 mb-2">Dia da Semana</label>
                     <div id="dieta-day-buttons" class="flex flex-wrap gap-2"></div>
                </div>

                <div>
                    <h3 class="font-bold text-white mb-2">Alimentos</h3>
                    <div id="alimentos-list" class="space-y-3"></div>
                    <button type="button" id="add-alimento-btn" class="mt-2 text-sm text-red-400 hover:text-red-300 transition"><i class="fas fa-plus mr-2"></i>Adicionar Alimento</button>
                </div>
                
                <div>
                    <h3 class="font-bold text-white mb-2">Suplementos</h3>
                    <div id="suplementos-list" class="space-y-3"></div>
                    <button type="button" id="add-suplemento-btn" class="mt-2 text-sm text-red-400 hover:text-red-300 transition"><i class="fas fa-plus mr-2"></i>Adicionar Suplemento</button>
                </div>
                
                <div>
                    <label for="dieta-observation-input" class="block text-sm font-medium text-neutral-300">Observações</label>
                    <textarea id="dieta-observation-input" placeholder="Ex: Comer 30min antes do treino" rows="2" class="mt-1 w-full form-input"></textarea>
                </div>

                <div class="pt-6 border-t border-neutral-700 flex-shrink-0">
                    <button type="submit" class="w-full bg-red-600 hover:bg-red-700 text-white font-bold py-3 rounded-lg transition">Guardar Refeição</button>
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
                    <div class="grid grid-cols-2 gap-4">
                        <div class="relative"><i class="fas fa-ruler-vertical absolute left-3 top-1/2 -translate-y-1/2 text-neutral-500"></i><input type="number" id="ia-altura" placeholder="Altura (cm)" class="form-input w-full pl-9" required></div>
                        <div class="relative"><i class="fas fa-weight-hanging absolute left-3 top-1/2 -translate-y-1/2 text-neutral-500"></i><input type="number" id="ia-peso" placeholder="Peso (kg)" class="form-input w-full pl-9" required></div>
                    </div>
                     <div class="grid grid-cols-2 gap-4">
                        <div class="relative"><i class="fas fa-cake-candles absolute left-3 top-1/2 -translate-y-1/2 text-neutral-500"></i><input type="number" id="ia-idade" placeholder="Idade" class="form-input w-full pl-9" required></div>
                        <div>
                            <select id="ia-sexo" class="w-full form-input" required><option value="" disabled selected>Sexo...</option><option value="masculino">Masculino</option><option value="feminino">Feminino</option></select>
                        </div>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-neutral-300 mb-2">Nível de Atividade Física</label>
                        <select id="ia-atividade" class="w-full form-input" required><option>Sedentário</option><option value="levemente ativo">Leve (1-2 dias/semana)</option><option value="moderadamente ativo">Moderado (3-4 dias/semana)</option><option value="muito ativo">Intenso (5-7 dias/semana)</option></select>
                    </div>
                </div>

                <div id="ia-step-2" class="ia-step hidden space-y-4">
                    <h3 class="font-bold text-white text-lg">Passo 2: O Seu Objetivo</h3>
                    <div class="grid grid-cols-2 gap-4" id="ia-objetivos-cards">
                        <div class="radio-card p-4 rounded-lg cursor-pointer text-center active" data-objetivo="perder peso"><i class="fas fa-weight-scale text-red-500 text-2xl mb-2"></i><p>Perder Peso</p></div>
                        <div class="radio-card p-4 rounded-lg cursor-pointer text-center" data-objetivo="ganhar massa"><i class="fas fa-dumbbell text-red-500 text-2xl mb-2"></i><p>Ganhar Massa</p></div>
                        <div class="radio-card p-4 rounded-lg cursor-pointer text-center" data-objetivo="manter saude"><i class="fas fa-heart-pulse text-red-500 text-2xl mb-2"></i><p>Manter Saúde</p></div>
                        <div class="radio-card p-4 rounded-lg cursor-pointer text-center" data-objetivo="performance"><i class="fas fa-running text-red-500 text-2xl mb-2"></i><p>Performance</p></div>
                    </div>
                </div>

                <div id="ia-step-3" class="ia-step hidden space-y-4">
                     <h3 class="font-bold text-white text-lg">Passo 3: Preferências</h3>
                     <div class="relative"><i class="fas fa-utensils absolute left-3 top-1/2 -translate-y-1/2 text-neutral-500"></i><input type="number" id="ia-refeicoes" placeholder="Nº de Refeições por Dia" class="form-input w-full pl-9" required></div>
                     <textarea id="ia-preferidos" rows="3" class="w-full form-input" placeholder="Alimentos preferidos (separados por vírgula)..."></textarea>
                     <textarea id="ia-restricoes" rows="2" class="w-full form-input" placeholder="Restrições ou alimentos a evitar..."></textarea>
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
            <p class="mt-4 font-bold text-lg">A I.A. está a forjar o seu plano...</p>
            <p class="text-sm text-neutral-400">Isto pode demorar alguns momentos.</p>
        </div>
    </div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', () => {
            
            const API_BASE_URL = `${window.location.origin}/api`;
            
            const loader = document.getElementById('loader');
            const addDietaBtn = document.getElementById('add-dieta-btn');
            const openIaModalBtn = document.getElementById('open-ia-modal-btn');
            const dietaModal = document.getElementById('dieta-modal');
            const iaModal = document.getElementById('ia-modal');
            const loadingModal = document.getElementById('loading-modal');
            const dietaForm = document.getElementById('dieta-form');
            const dietaModalTitle = document.getElementById('dieta-modal-title');
            const dietaIdInput = document.getElementById('dieta-id-input');
            const dietaNameInput = document.getElementById('dieta-name-input');
            const dietaTimeInput = document.getElementById('dieta-time-input');
            const dietaDayButtons = document.getElementById('dieta-day-buttons');
            const dietaObservationInput = document.getElementById('dieta-observation-input');
            const alimentosList = document.getElementById('alimentos-list');
            const addAlimentoBtn = document.getElementById('add-alimento-btn');
            const suplementosList = document.getElementById('suplementos-list');
            const addSuplementoBtn = document.getElementById('add-suplemento-btn');
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
                { id: 'segunda-feira', short: 'SEG', long: 'Segunda-Feira' }, { id: 'terca-feira', short: 'TER', long: 'Terça-Feira' },
                { id: 'quarta-feira', short: 'QUA', long: 'Quarta-Feira' }, { id: 'quinta-feira', short: 'QUI', long: 'Quinta-Feira' },
                { id: 'sexta-feira', short: 'SEX', long: 'Sexta-Feira' }, { id: 'sabado', short: 'SAB', long: 'Sábado' },
                { id: 'domingo', short: 'DOM', long: 'Domingo' }
            ];

            function initializeUI() {
                weekDays.forEach(day => {
                    weekDaysNav.innerHTML += `<button class="day-tab flex-1 px-2 py-2 text-xs md:px-4 md:text-sm font-bold text-neutral-400 rounded-full transition" data-day-id="${day.id}">${day.short}</button>`;
                    plannerContainer.innerHTML += `<div id="day-column-${day.id}" class="day-column hidden"><h2 class="text-xl font-bold text-white mb-4">${day.long}</h2><div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 meal-cards-container"></div></div>`;
                    dietaDayButtons.innerHTML += `<button type="button" class="day-select-button px-3 py-1 text-sm rounded-full" data-day-short="${day.short}">${day.short}</button>`;
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
                        const errorData = await response.json();
                        let errorMessages = errorData.message || response.statusText;
                        if(errorData.errors) {
                             errorMessages += "\n" + Object.values(errorData.errors).flat().join("\n");
                        }
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
            
            function renderDietas(dietas) {
                document.querySelectorAll('.meal-cards-container').forEach(c => c.innerHTML = '');

                dietas.forEach(dieta => {
                    const dayObject = weekDays.find(d => d.short.toLowerCase() === dieta.day.toLowerCase());
                    if (!dayObject) return;
                    
                    const dayColumn = document.getElementById(`day-column-${dayObject.id}`);
                    if (!dayColumn) return;

                    const container = dayColumn.querySelector('.meal-cards-container');
                    const alimentosHtml = dieta.alimentos.map(a => `<li class="text-xs text-neutral-400 truncate">${a.name} (${a.quantidade})</li>`).join('');
                    const suplementosHtml = dieta.suplementos.map(s => `<li class="text-xs text-neutral-500 truncate">${s.name} (${s.quantidade})</li>`).join('');
                    const observationHtml = dieta.observation ? `<p class="text-xs italic text-neutral-500 mt-2 border-t border-neutral-700 pt-2">${dieta.observation}</p>` : '';

                    const card = document.createElement('div');
                    card.className = "meal-card p-4 rounded-lg flex flex-col";
                    card.innerHTML = `
                        <div class="flex-grow">
                            <div class="flex justify-between items-start mb-3">
                                <div>
                                    <p class="font-bold text-md text-white">${dieta.name}</p>
                                    <p class="text-sm text-red-400 font-bold">${dieta.time.substring(0, 5)}</p>
                                </div>
                                <div class="flex space-x-1">
                                    <button class="edit-btn h-7 w-7 rounded-lg text-neutral-400 hover:bg-neutral-700 hover:text-white" data-id="${dieta.id}"><i class="fas fa-pencil-alt fa-xs"></i></button>
                                    <button class="delete-btn h-7 w-7 rounded-lg text-neutral-400 hover:bg-neutral-700 hover:text-red-400" data-id="${dieta.id}"><i class="fas fa-trash-alt fa-xs"></i></button>
                                </div>
                            </div>
                            <ul class="space-y-1 list-disc list-inside marker:text-red-500">${alimentosHtml}${suplementosHtml}</ul>
                            ${observationHtml}
                        </div>
                    `;
                    container.appendChild(card);
                });
                
                document.querySelectorAll('.meal-cards-container').forEach(c => {
                    if (c.innerHTML === '') {
                        c.innerHTML = `<div class="border-2 border-dashed border-neutral-800 rounded-lg p-10 text-center text-neutral-600"><i class="fas fa-utensils fa-2x mb-2"></i><p>Nenhuma refeição para este dia.</p></div>`;
                    }
                });
            }

            async function loadDietas() {
                loader.innerHTML = `<div class="animate-pulse grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6"><div class="h-40 bg-neutral-800 rounded-lg"></div><div class="h-40 bg-neutral-800 rounded-lg hidden md:block"></div><div class="h-40 bg-neutral-800 rounded-lg hidden lg:block"></div></div>`;
                plannerContainer.style.display = 'none';
                const response = await fetchAPI('/dieta');
                loader.innerHTML = '';
                plannerContainer.style.display = 'block';
                if (response?.data) {
                    renderDietas(response.data);
                } else if(response) { 
                    renderDietas([]);
                }
            }

            function createItemInput(type, item = {id: null, name: '', quantidade: '' }) {
                const div = document.createElement('div');
                div.className = 'flex items-center gap-2 item-row';
                if(item.id) { div.dataset.id = item.id; }
                div.innerHTML = `
                    <input type="text" placeholder="Nome do ${type}" value="${item.name}" class="flex-grow form-input" data-field="name" required>
                    <input type="text" placeholder="Qtd." value="${item.quantidade}" class="w-28 form-input" data-field="quantidade" required>
                    <button type="button" class="remove-item-btn text-neutral-500 hover:text-red-400"><i class="fas fa-times"></i></button>`;
                return div;
            }

            function openModal(modal) { modal.classList.add('active'); }
            function closeModal(modal) { modal.classList.remove('active'); }

            function openDietaModal(mode = 'add', dieta = null) {
                dietaForm.reset();
                alimentosList.innerHTML = '';
                suplementosList.innerHTML = '';
                dietaIdInput.value = '';

                document.querySelectorAll('.day-select-button').forEach(b => b.classList.remove('active'));

                if (mode === 'edit' && dieta) {
                    dietaModalTitle.textContent = 'Editar Refeição';
                    dietaIdInput.value = dieta.id;
                    dietaNameInput.value = dieta.name;
                    dietaTimeInput.value = dieta.time;
                    dietaObservationInput.value = dieta.observation;
                    
                    const dayBtn = dietaDayButtons.querySelector(`[data-day-short="${dieta.day}"]`);
                    if(dayBtn) dayBtn.classList.add('active');

                    dieta.alimentos.forEach(alimento => alimentosList.appendChild(createItemInput('alimento', alimento)));
                    dieta.suplementos.forEach(suplemento => suplementosList.appendChild(createItemInput('suplemento', suplemento)));
                } else {
                    dietaModalTitle.textContent = 'Nova Refeição';
                    alimentosList.appendChild(createItemInput('alimento'));
                    const activeDay = weekDaysNav.querySelector('.active')?.dataset.dayId;
                    const dayObject = weekDays.find(d => d.id === activeDay);
                    if(dayObject) {
                        const dayBtn = dietaDayButtons.querySelector(`[data-day-short="${dayObject.short}"]`);
                        if(dayBtn) dayBtn.classList.add('active');
                    }
                }
                openModal(dietaModal);
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

            addAlimentoBtn.addEventListener('click', () => alimentosList.appendChild(createItemInput('alimento')));
            addSuplementoBtn.addEventListener('click', () => suplementosList.appendChild(createItemInput('suplemento')));
            alimentosList.addEventListener('click', e => { if (e.target.closest('.remove-item-btn')) e.target.closest('.item-row').remove(); });
            suplementosList.addEventListener('click', e => { if (e.target.closest('.remove-item-btn')) e.target.closest('.item-row').remove(); });

            addDietaBtn.addEventListener('click', () => openDietaModal('add'));
            dietaModal.querySelector('.close-button').addEventListener('click', () => closeModal(dietaModal));
            openIaModalBtn.addEventListener('click', () => openModal(iaModal));
            iaModal.querySelector('.close-button').addEventListener('click', () => closeModal(iaModal));
            weekDaysNav.addEventListener('click', e => { if(e.target.matches('.day-tab')) switchDay(e.target.dataset.dayId); });

            dietaDayButtons.addEventListener('click', e => {
                const button = e.target.closest('.day-select-button');
                if (button) {
                    dietaDayButtons.querySelectorAll('.day-select-button').forEach(b => b.classList.remove('active'));
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
                    altura_cm: parseInt(document.getElementById('ia-altura').value),
                    peso_kg: parseInt(document.getElementById('ia-peso').value),
                    idade: parseInt(document.getElementById('ia-idade').value),
                    sexo: document.getElementById('ia-sexo').value,
                    nivel_atividade_fisica: document.getElementById('ia-atividade').value,
                    objetivo: iaObjetivosCards.querySelector('.active').dataset.objetivo,
                    refeicoes_por_dia: parseInt(document.getElementById('ia-refeicoes').value),
                    alimentos_preferidos: document.getElementById('ia-preferidos').value.split(',').map(s => s.trim()).filter(Boolean),
                    restricoes_alimentares: document.getElementById('ia-restricoes').value.split(',').map(s => s.trim()).filter(Boolean),
                };

                const aiResponse = await fetchAPI('/dieta/think-ai', { method: 'POST', body: JSON.stringify(payload) });
                
                // CORREÇÃO: A API de IA pode retornar a resposta diretamente como um array, não dentro de .data
                if (aiResponse && Array.isArray(aiResponse)) {
                    console.log(aiResponse)
                    
                    const creationPromises = aiResponse.map(refeicao => {
                        return fetchAPI('/dieta', { method: 'POST', body: JSON.stringify(refeicao) });
                    });
                    await Promise.all(creationPromises);
                    
                    closeModal(iaModal);
                    await loadDietas();
                    alert('Plano alimentar gerado e adicionado com sucesso!');
                } else {
                    console.error("Resposta da IA inválida:", aiResponse);
                    alert('A I.A. não retornou um plano válido. Tente novamente.');
                }
                
                closeModal(loadingModal);
            });

            plannerContainer.addEventListener('click', async (e) => {
                const editBtn = e.target.closest('.edit-btn');
                const deleteBtn = e.target.closest('.delete-btn');
                
                if (editBtn) {
                    const dietaId = editBtn.dataset.id;
                    const response = await fetchAPI(`/dieta/${dietaId}`);
                    if(response?.data) openDietaModal('edit', response.data);
                }

                if (deleteBtn) {
                    const dietaId = deleteBtn.dataset.id;
                    if (confirm('Tem a certeza que quer eliminar esta refeição?')) {
                        const response = await fetchAPI(`/dieta/${dietaId}`, { method: 'DELETE' });
                        if (response?.status) await loadDietas();
                    }
                }
            });

            dietaForm.addEventListener('submit', async (e) => {
                e.preventDefault();
                const id = dietaIdInput.value;
                const isEditing = !!id;

                const getItemsFromList = (listElement) => Array.from(listElement.querySelectorAll('.item-row')).map(row => ({
                    id: row.dataset.id || null,
                    name: row.querySelector('[data-field="name"]').value,
                    quantidade: row.querySelector('[data-field="quantidade"]').value,
                }));
                
                const activeDayBtn = dietaDayButtons.querySelector('.active');
                if (!activeDayBtn) {
                    alert('Por favor, selecione um dia da semana.');
                    return;
                }

                const data = {
                    name: dietaNameInput.value,
                    time: dietaTimeInput.value,
                    day: activeDayBtn.dataset.dayShort,
                    observation: dietaObservationInput.value,
                    alimentos: getItemsFromList(alimentosList),
                    suplementos: getItemsFromList(suplementosList),
                };

                const endpoint = isEditing ? `/dieta/${id}` : '/dieta';
                const method = isEditing ? 'PUT' : 'POST';

                const response = await fetchAPI(endpoint, { method, body: JSON.stringify(data) });

                if (response?.status) {
                    closeModal(dietaModal);
                    await loadDietas();
                }
            });

            initializeUI();
            loadDietas();
        });
</script>
@endpush

