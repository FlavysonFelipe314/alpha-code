@extends('layouts.app')

@section('title', 'Biblioteca')

@push('styles')
<style>

        /* Estilo da Navegação (Abas) - inspirado no NUTRI.AI */
        .tab-nav-button {
            transition: background-color 0.2s, color 0.2s;
            color: #a1a1aa; /* Cor neutra para inativos */
        }
        .tab-nav-button.active {
            background-color: #ef4444;
            color: white;
            box-shadow: 0 0 15px rgba(239, 68, 68, 0.5);
        }

        /* Estilo dos Cards (Livros/Videos) - inspirado no NUTRI.AI */
        .item-card {
            background: rgba(16, 16, 16, 0.6); /* Fundo bem escuro/transparente */
            border: 1px solid #262626; /* Borda sutil */
            backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(12px);
            transition: all 0.3s ease;
            position: relative; /* Para botões de ação */
        }
        .item-card:hover {
            transform: translateY(-5px);
            border-color: rgba(239, 68, 68, 0.5);
        }
        /* Botões de Ação no Card */
        .item-card .action-buttons {
            position: absolute;
            top: 0.75rem; /* 12px */
            right: 0.75rem; /* 12px */
            opacity: 0;
            transition: opacity 0.2s;
        }
        .item-card:hover .action-buttons {
            opacity: 1;
        }

        /* --- CSS CORRETO DO MODAL --- */
        .modal {
            position: fixed; 
            top: 0; left: 0; width: 100%; height: 100%;
            display: flex; 
            align-items: center; 
            justify-content: center;
            /* Fundo escuro com blur */
            background-color: rgba(0, 0, 0, 0.85); 
            backdrop-filter: blur(10px);
            z-index: 1000;
            /* Escondido por padrão */
            opacity: 0; 
            visibility: hidden; 
            transition: opacity 0.3s, visibility 0.3s;
        }
        /* Classe que ativa o modal */
        .modal.active {
            opacity: 1; 
            visibility: visible;
        }
        
        .modal-content {
            /* Estilo escuro (do NUTRI.AI) */
            background: #18181b; 
            border: 1px solid #3f3f46; 
            border-radius: 12px;
            padding: 2rem; 
            width: 90%; 
            max-width: 600px; /* Limite de largura */
            position: relative;
            /* Animação de entrada */
            transform: scale(0.95); 
            transition: transform 0.3s;
            box-shadow: 0 20px 25px -5px rgb(0 0 0 / 0.4), 0 8px 10px -6px rgb(0 0 0 / 0.4);
        }
        .modal.active .modal-content {
            transform: scale(1);
        }
        
        .close-button {
            position: absolute; 
            top: 1rem; right: 1rem; 
            background: none; border: none;
            color: #71717a; 
            font-size: 1.5rem; 
            cursor: pointer; 
            transition: color 0.3s;
        }
        .close-button:hover { 
            color: white; 
        }

        /* Inputs do Formulário (Estilo NUTRI.AI) */
        .form-input, .form-select {
            background-color: #27272a;
            border: 1px solid #3f3f46;
            border-radius: 0.5rem;
            padding: 0.75rem 1rem;
            color: white;
            transition: border-color 0.2s, box-shadow 0.2s;
            -webkit-appearance: none;
             -moz-appearance: none;
                  appearance: none;
        }
        .form-input:focus, .form-select:focus {
            outline: none;
            border-color: #ef4444;
            box-shadow: 0 0 0 2px rgba(239, 68, 68, 0.5);
        }
        .form-select {
           background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 20 20'%3e%3cpath stroke='%239ca3af' stroke-linecap='round' stroke-linejoin='round' stroke-width='1.5' d='M6 8l4 4 4-4'/%3e%3c/svg%3e");
           background-position: right 0.5rem center;
           background-repeat: no-repeat;
           background-size: 1.5em 1.5em;
           padding-right: 2.5rem;
        }
        /* Novo estilo para a barra de navegação de abas em largura total */
        .full-width-nav {
            padding-left: 1rem;
            padding-right: 1rem;
        }
    </style>
@endpush

@section('content')
<div class="container mx-auto p-4 md:p-6 lg:p-8">
    <!-- Header (Estilo NUTRI.AI) -->
    <header class="container mx-auto p-4 md:p-6 lg:p-8 flex justify-between items-center">
        <div class="flex items-center space-x-3">
            <i class="fas fa-book-open text-3xl text-red-500"></i> 
            <h1 class="text-2xl font-black text-white uppercase tracking-wider">Biblioteca</h1>
        </div>
        <div class="flex items-center space-x-3">
            <button id="add-item-btn" class="bg-red-600 hover:bg-red-700 text-white font-bold py-2 px-4 rounded-lg text-sm transition-transform hover:scale-105 flex items-center justify-center space-x-2">
                <i class="fas fa-plus"></i>
                <span>Adicionar Item</span>
            </button>
        </div>
    </header>

    <!-- Navegação por Abas (Estilo NUTRI.AI) - AGORA EM LARGURA TOTAL -->
    <nav class="full-width-nav flex justify-center space-x-1 md:space-x-2 my-8 p-1 bg-neutral-900/50 rounded-full border border-neutral-800 w-full md:max-w-4xl mx-auto">
        <button class="tab-nav-button flex-1 px-2 py-2 text-xs md:px-4 md:text-sm font-bold rounded-full" data-view="minha-biblioteca">
            <i class="fas fa-book mr-1 hidden md:inline"></i> Minha Biblioteca
        </button>
        <button class="tab-nav-button flex-1 px-2 py-2 text-xs md:px-4 md:text-sm font-bold rounded-full" data-view="lista-desejo">
            <i class="fas fa-heart mr-1 hidden md:inline"></i> Desejo
        </button>
        <button class="tab-nav-button flex-1 px-2 py-2 text-xs md:px-4 md:text-sm font-bold rounded-full" data-view="em-andamento">
            <i class="fas fa-hourglass-half mr-1 hidden md:inline"></i> Em Andamento
        </button>
        <button class="tab-nav-button flex-1 px-2 py-2 text-xs md:px-4 md:text-sm font-bold rounded-full" data-view="concluido">
            <i class="fas fa-check-circle mr-1 hidden md:inline"></i> Concluído
        </button>
    </nav>

    <!-- Conteúdo Principal -->
    <main class="container mx-auto p-4 md:p-6 lg:p-8 pt-0">
        
        <!-- View: Minha Biblioteca -->
        <div id="view-minha-biblioteca" class="view-content">
            <!-- Título da View (Estilo NUTRI.AI) -->
            <h2 class="text-3xl font-bold text-white mb-6">Minha Coleção Completa</h2>
            <!-- Grid de Itens -->
            <div id="container-minha-biblioteca" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
                <!-- Os cards serão inseridos aqui pelo JS -->
            </div>
        </div>

        <!-- View: Lista de Desejo -->
        <div id="view-lista-desejo" class="view-content hidden">
            <h2 class="text-3xl font-bold text-white mb-6">Lista de Desejo</h2>
            <div id="container-lista-desejo" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
                <!-- Os cards serão inseridos aqui pelo JS -->
            </div>
        </div>
        
        <!-- View: Em Andamento -->
        <div id="view-em-andamento" class="view-content hidden">
            <h2 class="text-3xl font-bold text-white mb-6">Em Andamento</h2>
            <div id="container-em-andamento" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
                <!-- Os cards serão inseridos aqui pelo JS -->
            </div>
        </div>
        
        <!-- View: Concluído -->
        <div id="view-concluido" class="view-content hidden">
            <h2 class="text-3xl font-bold text-white mb-6">Concluído</h2>
            <div id="container-concluido" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
                <!-- Os cards serão inseridos aqui pelo JS -->
            </div>
        </div>
        
        <!-- View: Leitura/Player -->
        <div id="view-leitura" class="view-content hidden">
            <button id="back-to-library" class="mb-4 text-neutral-400 hover:text-white transition"><i class="fas fa-arrow-left mr-2"></i> Voltar para a Biblioteca</button>
            <div class="bg-neutral-900 border border-neutral-800 rounded-lg p-6 flex flex-col items-center">
                <h2 class="text-3xl font-bold text-white mb-2" id="leitura-title">Título do Item</h2>
                <p class="text-md text-neutral-400 mb-6" id="leitura-type-author">Tipo - Autor</p>
                
                <!-- PDF Viewer -->
                <div id="pdf-viewer-container" class="hidden w-full max-w-5xl">
                    <div class="bg-neutral-800 rounded-lg p-4 mb-4 flex items-center justify-between">
                        <div class="flex items-center space-x-4">
                            <button id="pdf-prev-page" class="bg-neutral-700 hover:bg-neutral-600 text-white px-4 py-2 rounded-lg transition">
                                <i class="fas fa-chevron-left mr-2"></i>Anterior
                            </button>
                            <span class="text-neutral-300">
                                Página <span id="pdf-current-page">1</span> de <span id="pdf-total-pages">1</span>
                            </span>
                            <button id="pdf-next-page" class="bg-neutral-700 hover:bg-neutral-600 text-white px-4 py-2 rounded-lg transition">
                                Próxima<i class="fas fa-chevron-right ml-2"></i>
                            </button>
                        </div>
                        <div class="flex items-center space-x-2">
                            <input type="number" id="pdf-page-input" min="1" value="1" class="w-16 px-2 py-1 bg-neutral-700 text-white rounded text-center">
                            <button id="pdf-go-to-page" class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-lg transition">
                                Ir
                            </button>
                            <button id="pdf-zoom-out" class="bg-neutral-700 hover:bg-neutral-600 text-white px-3 py-2 rounded-lg transition">
                                <i class="fas fa-search-minus"></i>
                            </button>
                            <span id="pdf-zoom-level" class="text-neutral-300 px-2">100%</span>
                            <button id="pdf-zoom-in" class="bg-neutral-700 hover:bg-neutral-600 text-white px-3 py-2 rounded-lg transition">
                                <i class="fas fa-search-plus"></i>
                            </button>
                        </div>
                    </div>
                    <div id="pdf-viewer" class="bg-neutral-800 rounded-lg p-4 overflow-auto" style="max-height: 70vh;">
                        <canvas id="pdf-canvas" class="mx-auto"></canvas>
                    </div>
                </div>
                
                <!-- Video Player -->
                <div id="video-player-container" class="hidden w-full max-w-5xl">
                    <video id="video-player" controls class="w-full rounded-lg" style="max-height: 70vh;">
                        Seu navegador não suporta o elemento de vídeo.
                    </video>
                </div>

                <!-- Mensagem quando não há arquivo -->
                <div id="no-file-message" class="w-full max-w-4xl h-[60vh] bg-neutral-800 rounded-lg flex items-center justify-center">
                    <div class="text-center">
                        <i class="fas fa-file-alt text-6xl text-neutral-600 mb-4"></i>
                        <p class="text-neutral-600 italic">Este item não possui arquivo anexado.</p>
                    </div>
                </div>

                <div class="mt-6 w-full max-w-4xl flex justify-between items-center">
                    <button id="change-status-btn" class="bg-gray-700 hover:bg-gray-600 text-white font-bold py-2 px-4 rounded-lg transition">
                        <i class="fas fa-exchange-alt mr-2"></i>Mudar Status
                    </button>
                    <button id="mark-completed-btn" class="bg-red-600 hover:bg-red-700 text-white font-bold py-2 px-4 rounded-lg transition">
                        <i class="fas fa-check-circle mr-2"></i>Marcar como Concluído
                    </button>
                </div>
            </div>
        </div>

</div>

    <!-- --- O MODAL CORRIGIDO --- -->
    <!-- Esta é a estrutura correta do modal, que fica escondida até ser ativada -->
    <div id="item-modal" class="modal"> 
        <div class="modal-content max-h-[90vh] flex flex-col"> 
            <!-- Botão de Fechar -->
            <button class="close-button">&times;</button> 
            
            <h2 id="item-modal-title" class="text-2xl font-bold text-white mb-6 flex-shrink-0">Novo Conteúdo</h2> 
            
            <!-- Formulário do Modal -->
            <form id="item-form" class="flex-grow overflow-y-auto pr-4 space-y-4"> 
                <input type="hidden" id="item-id"> 
                
                <div>
                    <label for="item-title" class="block text-sm font-medium text-neutral-300 mb-1">Título</label>
                    <input type="text" id="item-title" placeholder="Ex: Livro de Culinária, Curso de Python" class="mt-1 w-full form-input" required>
                </div> 
                
                <div>
                    <label for="item-author" class="block text-sm font-medium text-neutral-300 mb-1">Autor/Criador</label>
                    <input type="text" id="item-author" placeholder="Ex: Maria Silva, Code Academy" class="mt-1 w-full form-input" required>
                </div> 
                
                <div class="grid grid-cols-2 gap-4"> 
                    <div> 
                        <label for="item-type" class="block text-sm font-medium text-neutral-300 mb-1">Tipo de Mídia</label> 
                        <select id="item-type" class="mt-1 w-full form-select" required> 
                            <option value="" disabled selected>Selecione...</option>
                            <option value="book">Livro (PDF/ePub)</option>
                            <option value="video">Vídeo</option>
                            <option value="audio">Áudio</option>
                        </select> 
                    </div> 
                    <div> 
                        <label for="item-status" class="block text-sm font-medium text-neutral-300 mb-1">Status</label> 
                        <select id="item-status" class="mt-1 w-full form-select" required> 
                            <option value="in-progress">Em Andamento</option>
                            <option value="completed">Concluído</option>
                            <option value="wishlist">Lista de Desejo</option>
                        </select> 
                    </div> 
                </div> 
                
                <!-- Campo de Upload de Arquivo -->
                <div>
                    <label for="item-file" class="block text-sm font-medium text-neutral-300 mb-1">Arquivo (PDF ou Vídeo)</label>
                    <input type="file" id="item-file" accept=".pdf,.mp4,.avi,.mkv,.mov,.wmv,.webm" class="mt-1 w-full form-input file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-red-600 file:text-white hover:file:bg-red-700">
                    <p class="text-xs text-neutral-500 mt-1">Formatos aceitos: PDF, MP4, AVI, MKV, MOV, WMV, WEBM (máx. 100MB)</p>
                    <div id="current-file-info" class="hidden mt-2 text-sm text-neutral-400"></div>
                </div>
                
                <!-- Campo de Progresso (aparece condicionalmente) -->
                <div id="progress-field" class="hidden">
                    <label for="item-progress" class="block text-sm font-medium text-neutral-300 mb-1">Progresso (%)</label>
                    <input type="number" step="1" min="0" max="100" id="item-progress" placeholder="0" class="mt-1 w-full form-input">
                </div>
                
                <div>
                    <label for="item-notes" class="block text-sm font-medium text-neutral-300 mb-1">Notas/Observações</label>
                    <textarea id="item-notes" rows="2" class="mt-1 w-full form-input"></textarea>
                </div> 
                
                <!-- Botão de Submissão -->
                <div class="pt-6 border-t border-neutral-700 flex-shrink-0">
                    <button type="submit" class="w-full bg-red-600 hover:bg-red-700 text-white font-bold py-3 rounded-lg transition">Guardar Item</button>
                </div> 
            </form> 
        </div> 
    </div>
    <!-- --- FIM DO MODAL --- -->
@endsection

@push('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.11.174/pdf.min.js"></script>
<script>
    pdfjsLib.GlobalWorkerOptions.workerSrc = 'https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.11.174/pdf.worker.min.js';
</script>
<script>
    document.addEventListener('DOMContentLoaded', () => {
            
            // --- Seletores DOM ---
            const navButtons = document.querySelectorAll('.tab-nav-button');
            const viewContents = document.querySelectorAll('.view-content');
            const itemModal = document.getElementById('item-modal');
            const addItemBtn = document.getElementById('add-item-btn');
            const itemForm = document.getElementById('item-form');
            const progressField = document.getElementById('progress-field');
            const itemStatusSelect = document.getElementById('item-status');
            const mainContainer = document.querySelector('.container');
            const backToLibraryBtn = document.getElementById('back-to-library');

            // --- Dados carregados via API ---
            let libraryData = [];

            // --- Funções do Modal ---
            function openModal(modal) {
                modal.classList.add('active');
            }
            function closeModal(modal) { 
                modal.classList.remove('active');
                itemForm.reset(); // Limpa o formulário ao fechar
                progressField.classList.add('hidden'); // Esconde o campo de progresso
            }

            // --- Funções de Renderização ---
            
            // Cria o HTML para um único card de item
            function createItemCardHTML(item) {
                let typeLabel, iconClass, typeColorClass, statusTag = '';

                // Define ícone e cor baseado no tipo
                switch (item.type) {
                    case 'book': 
                        typeLabel = 'Livro'; iconClass = 'fa-book'; typeColorClass = 'text-red-400'; 
                        break;
                    case 'video': 
                        typeLabel = 'Vídeo'; iconClass = 'fa-video'; typeColorClass = 'text-blue-400'; 
                        break;
                    case 'audio': 
                        typeLabel = 'Áudio'; iconClass = 'fa-headphones'; typeColorClass = 'text-purple-400'; 
                        break;
                    default: 
                        typeLabel = 'Conteúdo'; iconClass = 'fa-question'; typeColorClass = 'text-neutral-400';
                }

                // Define a tag de status (Progresso, Concluído, Desejo)
                if (item.status === 'completed') {
                    statusTag = `<p class="text-xs text-green-500 mt-3 font-semibold"><i class="fas fa-check-circle mr-1"></i> Concluído</p>`;
                } else if (item.status === 'wishlist') {
                    statusTag = `<p class="text-xs text-blue-400 mt-3 font-semibold"><i class="fas fa-heart mr-1"></i> Lista de Desejo</p>`;
                } else if (item.status === 'in-progress') {
                    const progressBar = `<div class="mt-2 h-1 bg-gray-700 rounded"><div class="bg-red-500 h-1 rounded" style="width: ${item.progress}%;"></div></div>`;
                    statusTag = `${progressBar}<p class="text-xs text-neutral-500 mt-1">${item.progress}% Completo</p>`;
                }

                return `
                    <div class="item-card p-4 rounded-lg flex flex-col" data-item-id="${item.id}" data-status="${item.status}">
                        <div class="flex-grow">
                            <!-- Botões de Ação -->
                            <div class="action-buttons">
                                <button class="edit-item-btn h-7 w-7 rounded-lg text-neutral-400 hover:bg-neutral-700 hover:text-white" data-id="${item.id}" title="Editar">
                                    <i class="fas fa-pencil-alt fa-xs"></i>
                                </button>
                                <button class="delete-item-btn h-7 w-7 rounded-lg text-neutral-400 hover:bg-neutral-700 hover:text-red-400" data-id="${item.id}" title="Eliminar">
                                    <i class="fas fa-trash-alt fa-xs"></i>
                                </button>
                            </div>

                            <!-- Informações do Card -->
                            <div class="flex items-center space-x-3 mb-3">
                                <i class="fas ${iconClass} ${typeColorClass} fa-lg"></i>
                                <span class="text-xs font-semibold ${typeColorClass} uppercase">${typeLabel}</span>
                            </div>
                            <h3 class="text-md font-bold text-white truncate mb-1">${item.title}</h3>
                            <p class="text-sm text-neutral-400 truncate mb-2">${item.author}</p>
                            ${statusTag}
                        </div>
                    </div>
                `;
            }

            // Renderiza todos os itens nos containers corretos
            function renderAllItems() {
                const containers = {
                    'minha-biblioteca': document.getElementById('container-minha-biblioteca'),
                    'lista-desejo': document.getElementById('container-lista-desejo'),
                    'em-andamento': document.getElementById('container-em-andamento'),
                    'concluido': document.getElementById('container-concluido')
                };

                // Limpa todos os containers
                Object.values(containers).forEach(c => c.innerHTML = '');

                libraryData.forEach(item => {
                    const cardHTML = createItemCardHTML(item);
                    
                    // Adiciona em "Minha Biblioteca" (todos)
                    containers['minha-biblioteca'].innerHTML += cardHTML;

                    // Adiciona na sua respectiva categoria
                    if (item.status === 'wishlist' && containers['lista-desejo']) {
                        containers['lista-desejo'].innerHTML += cardHTML;
                    } else if (item.status === 'in-progress' && containers['em-andamento']) {
                        containers['em-andamento'].innerHTML += cardHTML;
                    } else if (item.status === 'completed' && containers['concluido']) {
                        containers['concluido'].innerHTML += cardHTML;
                    }
                });

                // Adiciona mensagem de vazio se necessário
                Object.values(containers).forEach(container => {
                    if (container.innerHTML === '') {
                        container.innerHTML = `<div class="col-span-full text-center text-neutral-600 border-2 border-dashed border-neutral-800 rounded-lg p-10"><i class="fas fa-box-open fa-2x mb-2"></i><p>Nenhum item nesta seção.</p></div>`;
                    }
                });
            }

            async function fetchAllItems() {
                try {
                    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
                    const res = await fetch('/api/biblioteca', {
                        credentials: 'include',
                        headers: {
                            'Accept': 'application/json',
                            'X-Requested-With': 'XMLHttpRequest',
                            'X-CSRF-TOKEN': csrfToken || ''
                        }
                    });
                    if (!res.ok) return [];
                    const data = await res.json();
                    return data.data || data; // Suporta ambos os formatos
                } catch (error) {
                    console.error('Erro ao buscar itens:', error);
                    return [];
                }
            }

            async function loadAndRender() {
                libraryData = await fetchAllItems();
                renderAllItems();
            }

            // --- Funções de Navegação ---
            function switchView(viewId) {
                viewContents.forEach(view => view.classList.add('hidden'));
                navButtons.forEach(link => link.classList.remove('active'));

                const targetView = document.getElementById(`view-${viewId}`);
                const targetLink = document.querySelector(`.tab-nav-button[data-view="${viewId}"]`);

                if(targetView) targetView.classList.remove('hidden');
                if(targetLink) targetLink.classList.add('active');
            }
            
            // --- Event Listeners ---

            // Navegação por Abas
            navButtons.forEach(button => {
                button.addEventListener('click', (e) => {
                    e.preventDefault();
                    switchView(button.dataset.view);
                });
            });

            // Abrir Modal (Botão Adicionar Item)
            addItemBtn.addEventListener('click', () => {
                document.getElementById('item-modal-title').textContent = 'Novo Conteúdo';
                document.getElementById('item-id').value = ''; // Limpa o ID
                itemStatusSelect.value = 'in-progress'; // Define o padrão
                progressField.classList.remove('hidden'); // Mostra o progresso
                document.getElementById('item-progress').required = true;
                openModal(itemModal);
            });

            // Fechar Modal
            itemModal.querySelector('.close-button').addEventListener('click', () => closeModal(itemModal));

            // Mudar visibilidade do campo de Progresso no Modal
            itemStatusSelect.addEventListener('change', (e) => {
                const isProgressVisible = e.target.value === 'in-progress';
                progressField.classList.toggle('hidden', !isProgressVisible);
                document.getElementById('item-progress').required = isProgressVisible;
                if (!isProgressVisible) {
                    document.getElementById('item-progress').value = ''; // Limpa o valor se esconder
                }
            });
            
            // Submissão do Formulário (Adicionar ou Editar)
            itemForm.addEventListener('submit', async (e) => {
                e.preventDefault();
                const id = document.getElementById('item-id').value;
                const isEditing = !!id;
                const status = itemStatusSelect.value;
                const progress = status === 'in-progress' ? parseInt(document.getElementById('item-progress').value || 0) : (status === 'completed' ? 100 : 0);

                const formData = new FormData();
                formData.append('title', document.getElementById('item-title').value);
                formData.append('author', document.getElementById('item-author').value);
                formData.append('type', document.getElementById('item-type').value);
                formData.append('status', status);
                formData.append('progress', progress);
                formData.append('notes', document.getElementById('item-notes').value || '');
                
                const fileInput = document.getElementById('item-file');
                if (fileInput.files.length > 0) {
                    formData.append('file', fileInput.files[0]);
                }

                try {
                    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
                    const endpoint = isEditing ? `/api/biblioteca/${id}` : '/api/biblioteca';
                    const method = isEditing ? 'PUT' : 'POST';
                    
                    const response = await fetch(endpoint, {
                        method: method,
                        credentials: 'include',
                        body: formData,
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest',
                            'X-CSRF-TOKEN': csrfToken || ''
                        }
                    });
                    
                    if (response.ok) {
                        closeModal(itemModal);
                        await loadAndRender();
                    } else {
                        const errorData = await response.json();
                        alert('Erro ao salvar: ' + (errorData.message || 'Erro desconhecido'));
                    }
                } catch (error) {
                    console.error('Erro:', error);
                    alert('Erro ao salvar item');
                }
            });

            // Event Listener Principal (para Editar, Eliminar e Abrir Leitura)
            mainContainer.addEventListener('click', (e) => {
                const editBtn = e.target.closest('.edit-item-btn');
                const deleteBtn = e.target.closest('.delete-item-btn');
                const readCard = e.target.closest('.item-card');
                
                if (editBtn) {
                    e.preventDefault();
                    e.stopPropagation(); // Impede que o clique no card acione a leitura
                    const itemId = parseInt(editBtn.dataset.id);
                    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
                    fetch(`/api/biblioteca/${itemId}`, {
                        credentials: 'include',
                        headers: {
                            'Accept': 'application/json',
                            'X-Requested-With': 'XMLHttpRequest',
                            'X-CSRF-TOKEN': csrfToken || ''
                        }
                    }).then(r => r.ok && r.json()).then(data => {
                        const item = data.data || data;
                        if (!item) return;
                        document.getElementById('item-modal-title').textContent = 'Editar Conteúdo';
                        document.getElementById('item-id').value = item.id;
                        document.getElementById('item-title').value = item.title;
                        document.getElementById('item-author').value = item.author || '';
                        document.getElementById('item-type').value = item.type || '';
                        itemStatusSelect.value = item.status;
                        document.getElementById('item-notes').value = item.notes || '';
                        const isProgressVisible = item.status === 'in-progress';
                        progressField.classList.toggle('hidden', !isProgressVisible);
                        document.getElementById('item-progress').required = isProgressVisible;
                        document.getElementById('item-progress').value = item.progress;
                        
                        // Mostra informações do arquivo atual se existir
                        const currentFileInfo = document.getElementById('current-file-info');
                        if (item.data?.file_path || item.file_path) {
                            const filePath = item.data?.file_path || item.file_path;
                            currentFileInfo.classList.remove('hidden');
                            currentFileInfo.innerHTML = `<i class="fas fa-file mr-2"></i>Arquivo atual: ${filePath.split('/').pop()}`;
                        } else {
                            currentFileInfo.classList.add('hidden');
                        }
                        
                        // Limpa o input de arquivo ao editar (usuário pode manter ou trocar)
                        document.getElementById('item-file').value = '';
                        openModal(itemModal);
                    });
                } 
                else if (deleteBtn) {
                    e.preventDefault();
                    e.stopPropagation();
                    const itemId = parseInt(deleteBtn.dataset.id);
                    if (confirm('Tem a certeza que quer eliminar este item?')) {
                        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
                        fetch(`/api/biblioteca/${itemId}`, { 
                            method: 'DELETE',
                            credentials: 'include',
                            headers: {
                                'Accept': 'application/json',
                                'X-Requested-With': 'XMLHttpRequest',
                                'X-CSRF-TOKEN': csrfToken || ''
                            }
                        }).then(r => { if (r.ok) loadAndRender(); });
                    }
                }
                else if (readCard) {
                    e.preventDefault();
                    const itemId = parseInt(readCard.dataset.itemId);
                    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
                    fetch(`/api/biblioteca/${itemId}`, {
                        credentials: 'include',
                        headers: {
                            'Accept': 'application/json',
                            'X-Requested-With': 'XMLHttpRequest',
                            'X-CSRF-TOKEN': csrfToken || ''
                        }
                    }).then(r => r.ok && r.json()).then(data => {
                        const item = data.data || data;
                        if (!item) return;
                        if (item.status !== 'wishlist') {
                            openContentViewer(item);
                        } else {
                            alert("Este item está na sua Lista de Desejo. Mude o status para 'Em Andamento' para começar a vê-lo.");
                        }
                    });
                }
            });

            // Voltar da View de Leitura
            backToLibraryBtn.addEventListener('click', (e) => {
                e.preventDefault();
                // Volta para a aba que estava ativa (ou padrão para 'minha-biblioteca')
                const activeTab = document.querySelector('.tab-nav-button.active')?.dataset.view || 'minha-biblioteca';
                switchView(activeTab);
            });

            // Variáveis globais para PDF viewer
            let currentPdfDoc = null;
            let currentPdfPage = 1;
            let currentPdfScale = 1.0;
            let currentViewingItem = null;

            // Função para abrir visualizador de conteúdo (PDF ou Vídeo)
            function openContentViewer(item) {
                currentViewingItem = item;
                document.getElementById('leitura-title').textContent = item.title;
                document.getElementById('leitura-type-author').textContent = `${item.type?.charAt(0).toUpperCase() + item.type?.slice(1)} - ${item.author || 'Sem autor'}`;
                
                const filePath = item.file_path;
                const fileType = item.file_type || item.type;
                
                // Esconde todos os visualizadores
                document.getElementById('pdf-viewer-container').classList.add('hidden');
                document.getElementById('video-player-container').classList.add('hidden');
                document.getElementById('no-file-message').classList.add('hidden');
                
                if (!filePath) {
                    document.getElementById('no-file-message').classList.remove('hidden');
                } else if (fileType?.includes('pdf') || item.type === 'book') {
                    loadPDFViewer(`/storage/${filePath}`);
                } else if (fileType?.includes('video') || ['mp4', 'avi', 'mkv', 'mov', 'wmv', 'webm'].some(ext => filePath.toLowerCase().endsWith(ext))) {
                    loadVideoPlayer(`/storage/${filePath}`);
                } else {
                    document.getElementById('no-file-message').classList.remove('hidden');
                }
                
                switchView('leitura');
            }

            // Função para carregar visualizador de PDF
            function loadPDFViewer(pdfUrl) {
                const container = document.getElementById('pdf-viewer-container');
                const canvas = document.getElementById('pdf-canvas');
                container.classList.remove('hidden');
                currentPdfPage = 1;
                currentPdfScale = 1.0;
                
                pdfjsLib.getDocument(pdfUrl).promise.then((pdf) => {
                    currentPdfDoc = pdf;
                    document.getElementById('pdf-total-pages').textContent = pdf.numPages;
                    renderPDFPage(currentPdfPage);
                }).catch(error => {
                    console.error('Erro ao carregar PDF:', error);
                    alert('Erro ao carregar o PDF');
                });
            }

            // Função para renderizar página do PDF
            function renderPDFPage(pageNum) {
                if (!currentPdfDoc) return;
                
                currentPdfDoc.getPage(pageNum).then((page) => {
                    const viewport = page.getViewport({ scale: currentPdfScale });
                    const canvas = document.getElementById('pdf-canvas');
                    const context = canvas.getContext('2d');
                    canvas.height = viewport.height;
                    canvas.width = viewport.width;
                    
                    const renderContext = {
                        canvasContext: context,
                        viewport: viewport
                    };
                    
                    page.render(renderContext);
                    document.getElementById('pdf-current-page').textContent = pageNum;
                    document.getElementById('pdf-page-input').value = pageNum;
                    document.getElementById('pdf-zoom-level').textContent = Math.round(currentPdfScale * 100) + '%';
                });
            }

            // Event listeners para controles do PDF
            document.getElementById('pdf-prev-page')?.addEventListener('click', () => {
                if (currentPdfDoc && currentPdfPage > 1) {
                    currentPdfPage--;
                    renderPDFPage(currentPdfPage);
                }
            });

            document.getElementById('pdf-next-page')?.addEventListener('click', () => {
                if (currentPdfDoc && currentPdfPage < currentPdfDoc.numPages) {
                    currentPdfPage++;
                    renderPDFPage(currentPdfPage);
                }
            });

            document.getElementById('pdf-go-to-page')?.addEventListener('click', () => {
                const pageNum = parseInt(document.getElementById('pdf-page-input').value);
                if (currentPdfDoc && pageNum >= 1 && pageNum <= currentPdfDoc.numPages) {
                    currentPdfPage = pageNum;
                    renderPDFPage(currentPdfPage);
                }
            });

            document.getElementById('pdf-zoom-in')?.addEventListener('click', () => {
                currentPdfScale += 0.25;
                renderPDFPage(currentPdfPage);
            });

            document.getElementById('pdf-zoom-out')?.addEventListener('click', () => {
                if (currentPdfScale > 0.25) {
                    currentPdfScale -= 0.25;
                    renderPDFPage(currentPdfPage);
                }
            });

            // Função para carregar player de vídeo
            function loadVideoPlayer(videoUrl) {
                const container = document.getElementById('video-player-container');
                const video = document.getElementById('video-player');
                container.classList.remove('hidden');
                video.src = videoUrl;
                video.load();
            }

            // Função para mudar status rapidamente
            document.getElementById('change-status-btn')?.addEventListener('click', async () => {
                if (!currentViewingItem) return;
                
                const currentStatus = currentViewingItem.status;
                let newStatus;
                if (currentStatus === 'wishlist') newStatus = 'in-progress';
                else if (currentStatus === 'in-progress') newStatus = 'completed';
                else newStatus = 'wishlist';
                
                try {
                    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
                    const response = await fetch(`/api/biblioteca/${currentViewingItem.id}`, {
                        method: 'PUT',
                        credentials: 'include',
                        headers: { 
                            'Content-Type': 'application/json',
                            'Accept': 'application/json',
                            'X-Requested-With': 'XMLHttpRequest',
                            'X-CSRF-TOKEN': csrfToken || ''
                        },
                        body: JSON.stringify({ ...currentViewingItem, status: newStatus })
                    });
                    
                    if (response.ok) {
                        await loadAndRender();
                        const updatedItem = libraryData.find(i => i.id === currentViewingItem.id);
                        if (updatedItem) {
                            currentViewingItem = updatedItem;
                        }
                        alert('Status atualizado com sucesso!');
                    }
                } catch (error) {
                    console.error('Erro ao atualizar status:', error);
                    alert('Erro ao atualizar status');
                }
            });

            document.getElementById('mark-completed-btn')?.addEventListener('click', async () => {
                if (!currentViewingItem) return;
                
                try {
                    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
                    const response = await fetch(`/api/biblioteca/${currentViewingItem.id}`, {
                        method: 'PUT',
                        credentials: 'include',
                        headers: { 
                            'Content-Type': 'application/json',
                            'Accept': 'application/json',
                            'X-Requested-With': 'XMLHttpRequest',
                            'X-CSRF-TOKEN': csrfToken || ''
                        },
                        body: JSON.stringify({ ...currentViewingItem, status: 'completed', progress: 100 })
                    });
                    
                    if (response.ok) {
                        await loadAndRender();
                        alert('Item marcado como concluído!');
                    }
                } catch (error) {
                    console.error('Erro ao marcar como concluído:', error);
                    alert('Erro ao atualizar');
                }
            });

            // --- Inicialização ---
            loadAndRender();
            switchView('minha-biblioteca'); // Define a view inicial

        });
</script>
@endpush