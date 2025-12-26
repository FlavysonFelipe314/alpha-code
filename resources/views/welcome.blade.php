@extends('layouts.app')

@section('title', 'Bloco de Notas')

@push('styles')
<style>
        /* Estilos do Quill Editor - Tema Escuro */
        #note-content .ql-container {
            font-family: 'Inter', sans-serif;
            font-size: 16px;
            color: #e5e7eb;
            background: transparent;
            border: none;
            height: 100%;
            display: flex;
            flex-direction: column;
        }
        #note-content .ql-container.ql-snow {
            border: none;
        }
        #note-content .ql-editor {
            flex: 1;
            color: #e5e7eb;
            padding: 1rem;
            overflow-y: auto;
        }
        #note-content .ql-editor.ql-blank::before {
            color: #6b7280;
            font-style: normal;
        }
        #note-content .ql-editor p,
        #note-content .ql-editor ol,
        #note-content .ql-editor ul,
        #note-content .ql-editor pre,
        #note-content .ql-editor blockquote,
        #note-content .ql-editor h1,
        #note-content .ql-editor h2,
        #note-content .ql-editor h3 {
            color: #e5e7eb;
        }
        #note-content .ql-toolbar.ql-snow {
            background: transparent;
            border: none;
            border-bottom: 1px solid #3f3f46;
            padding: 0.5rem;
        }
        #note-content .ql-toolbar.ql-snow .ql-stroke {
            stroke: #a1a1aa;
        }
        #note-content .ql-toolbar.ql-snow .ql-fill {
            fill: #a1a1aa;
        }
        #note-content .ql-toolbar.ql-snow button:hover .ql-stroke,
        #note-content .ql-toolbar.ql-snow button.ql-active .ql-stroke {
            stroke: #ef4444;
        }
        #note-content .ql-toolbar.ql-snow button:hover .ql-fill,
        #note-content .ql-toolbar.ql-snow button.ql-active .ql-fill {
            fill: #ef4444;
        }
        #note-content .ql-toolbar.ql-snow .ql-picker-label {
            color: #a1a1aa;
        }
        #note-content .ql-toolbar.ql-snow .ql-picker-label:hover {
            color: #ef4444;
        }
        #note-content .ql-toolbar.ql-snow .ql-picker-options {
            background: #27272a;
            border: 1px solid #3f3f46;
            color: #e5e7eb;
        }
        #note-content .ql-toolbar.ql-snow .ql-picker-item {
            color: #e5e7eb;
        }
        #note-content .ql-toolbar.ql-snow .ql-picker-item:hover {
            background: #3f3f46;
        }
        /* Syntax highlighting para blocos de código */
        #note-content .ql-syntax {
            background: #1e1e1e !important;
            color: #d4d4d4 !important;
            border: 1px solid #3f3f46;
            border-radius: 4px;
            padding: 1rem;
            margin: 1rem 0;
            overflow-x: auto;
            font-family: 'Courier New', monospace;
            font-size: 14px;
        }
        /* Links no editor */
        #note-content .ql-editor a {
            color: #60a5fa;
        }
        #note-content .ql-editor a:hover {
            color: #93c5fd;
        }
        .modal {
            position: fixed; top: 0; left: 0; width: 100%; height: 100%;
            display: flex; align-items: center; justify-content: center;
            background-color: rgba(0, 0, 0, 0.8); backdrop-filter: blur(8px);
            z-index: 1000; opacity: 0; visibility: hidden; transition: opacity 0.3s, visibility 0.3s;
        }
        .modal.active { opacity: 1; visibility: visible; }
        .modal-content {
            background: #18181b; border: 1px solid #3f3f46; border-radius: 12px;
            padding: 2rem; width: 90%; max-width: 400px;
            transform: scale(0.95); transition: transform 0.3s; position: relative;
        }
        .modal.active .modal-content { transform: scale(1); }
        .close-button {
            position: absolute; top: 1rem; right: 1rem; background: none; border: none;
            color: #71717a; font-size: 1.5rem; cursor: pointer; transition: color 0.3s;
        }
        .close-button:hover { color: white; }
    </style>
@endpush

@section('content')
<div class="h-screen flex flex-col p-0">
        <header class="flex items-center space-x-4 p-4 border-b border-neutral-800 flex-shrink-0">
            <h1 class="text-2xl font-black text-white uppercase tracking-wider">Bloco de notas</h1>
        </header>

        <div class="flex-grow flex gap-0 overflow-hidden">
            <!-- Coluna Esquerda: Tópicos e Anotações -->
            <div id="sidebar" class="w-64 flex flex-col bg-black/30 border-r border-neutral-800 p-4 overflow-y-auto">
                <div class="flex justify-between items-center mb-4 flex-shrink-0">
                    <h2 class="font-bold text-white uppercase tracking-wider">Tópicos</h2>
                    <button id="add-topic-btn" title="Adicionar Novo Tópico" class="text-neutral-400 hover:text-white transition h-8 w-8 rounded-lg hover:bg-neutral-700">
                        <i class="fas fa-plus"></i>
                    </button>
                </div>
                <div id="topic-list" class="flex-grow overflow-y-auto pr-2">
                    <div id="study-guide-loader" class="text-center text-neutral-500 mt-10">
                        <i class="fas fa-spinner fa-spin fa-2x"></i>
                        <p class="mt-2 text-sm">A carregar...</p>
                    </div>
                </div>
            </div>

            <!-- Coluna Direita: Editor -->
            <div id="editor-container" class="flex-1 flex flex-col bg-neutral-900/50 overflow-hidden">
                <div id="note-editor" class="flex-grow flex flex-col p-6 hidden overflow-hidden">
                    <div class="flex items-center justify-between mb-4 flex-shrink-0">
                        <input type="text" id="note-title" placeholder="Título da Anotação..." class="bg-transparent text-2xl font-bold text-white focus:outline-none flex-1">
                        <label for="note-file-input" class="ml-4 cursor-pointer bg-neutral-700 hover:bg-neutral-600 text-white font-semibold py-2 px-4 rounded-lg text-sm transition border border-neutral-600">
                            <i class="fas fa-paperclip mr-2"></i>Anexar Arquivo
                        </label>
                        <input type="file" id="note-file-input" class="hidden" accept=".pdf,.doc,.docx,.txt,.jpg,.jpeg,.png,.gif">
                    </div>
                    <div id="file-info" class="mb-2 text-sm text-neutral-400 flex items-center space-x-2 hidden"></div>
                    <div id="editor-toolbar" class="mb-2 border-y border-neutral-700 py-2 flex-shrink-0"></div>
                    <div id="note-content" class="flex-grow text-neutral-300 overflow-y-auto"></div>
                    <div class="flex space-x-2 mt-4 flex-shrink-0">
                        <button id="delete-note-btn" class="bg-transparent hover:bg-red-900/50 text-red-500 font-bold py-2 px-4 rounded-lg text-sm transition border border-red-800">Eliminar</button>
                        <button id="save-note-btn" class="flex-grow bg-red-600 hover:bg-red-700 text-white font-bold py-2 rounded-lg text-sm transition">Guardar Anotação</button>
                    </div>
                </div>
                <div id="placeholder-view" class="flex flex-col items-center justify-center h-full text-neutral-600">
                    <i class="fas fa-file-alt fa-4x mb-4"></i>
                    <p class="text-lg">Selecione uma anotação para editar</p>
                    <p class="text-sm">ou crie um novo tópico e anotação para começar.</p>
                </div>
            </div>
        </div>
</div>

    <!-- Modal para Tópicos -->
    <div id="topic-modal" class="modal">
        <div class="modal-content">
            <button class="close-button">&times;</button>
            <h2 id="topic-modal-title" class="text-2xl font-bold text-white mb-6">Novo Tópico</h2>
            <form id="topic-form">
                <div>
                    <label for="topic-name-input" class="block text-sm font-medium text-neutral-300">Nome do Tópico</label>
                    <input type="text" id="topic-name-input" class="mt-1 block w-full bg-neutral-800 border border-neutral-700 rounded-md shadow-sm py-2 px-3 text-white focus:outline-none focus:ring-red-500 focus:border-red-500" required>
                    <input type="hidden" id="topic-id-input">
                </div>
                <button type="submit" class="w-full mt-6 bg-red-600 hover:bg-red-700 text-white font-bold py-2 rounded-lg transition">Guardar</button>
            </form>
        </div>
    </div>
@endsection

@push('scripts')
<script src="https://cdn.quilljs.com/1.3.6/quill.js"></script>
<link href="https://cdn.quilljs.com/1.3.6/quill.snow.css" rel="stylesheet">
<script src="https://cdnjs.cloudflare.com/ajax/libs/prismjs/1.29.0/prism.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/prismjs/1.29.0/components/prism-javascript.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/prismjs/1.29.0/components/prism-python.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/prismjs/1.29.0/components/prism-php.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/prismjs/1.29.0/components/prism-css.min.js"></script>
<link href="https://cdnjs.cloudflare.com/ajax/libs/prismjs/1.29.0/themes/prism-tomorrow.min.css" rel="stylesheet">
<script>
    document.addEventListener('DOMContentLoaded', () => {
            
            const API_BASE_URL = `${window.location.origin}/api`;
            let studyData = {};
            let activeTopicId = null;
            let activeNoteId = null;

            const topicListEl = document.getElementById('topic-list');
            const addTopicBtn = document.getElementById('add-topic-btn');
            const noteEditorEl = document.getElementById('note-editor');
            const placeholderViewEl = document.getElementById('placeholder-view');
            const noteTitleInput = document.getElementById('note-title');
            const noteContentArea = document.getElementById('note-content');
            const saveNoteBtn = document.getElementById('save-note-btn');
            const deleteNoteBtn = document.getElementById('delete-note-btn');
            const loaderEl = document.getElementById('study-guide-loader');
            
            // Inicializar Quill Editor
            let quillEditor = null;
            function initQuillEditor() {
                if (quillEditor) {
                    quillEditor.root.innerHTML = '';
                    return;
                }
                
                quillEditor = new Quill('#note-content', {
                    theme: 'snow',
                    modules: {
                        toolbar: [
                            [{ 'header': [1, 2, 3, false] }],
                            ['bold', 'italic', 'underline', 'strike'],
                            [{ 'list': 'ordered'}, { 'list': 'bullet' }],
                            [{ 'color': [] }, { 'background': [] }],
                            [{ 'align': [] }],
                            ['link', 'image', 'code-block'],
                            ['blockquote'],
                            ['clean']
                        ]
                    },
                    placeholder: 'Comece a escrever sua anotação...'
                });
                
                // Estilo customizado para o editor
                const toolbar = noteContentArea.parentElement.querySelector('.ql-toolbar');
                if (toolbar) {
                    toolbar.style.background = 'transparent';
                    toolbar.style.border = 'none';
                    toolbar.style.borderBottom = '1px solid #3f3f46';
                    toolbar.style.padding = '0.5rem';
                }
            }
            const topicModal = document.getElementById('topic-modal');
            const topicForm = document.getElementById('topic-form');
            const topicModalTitle = document.getElementById('topic-modal-title');
            const topicNameInput = document.getElementById('topic-name-input');
            const topicIdInput = document.getElementById('topic-id-input');

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
                    if (!response.ok) throw new Error(`Erro na API: ${response.statusText}`);
                    if (response.status === 204 || (response.status === 200 && options.method === 'DELETE')) return { status: true };
                    return await response.json();
                } catch (error) {
                    console.error(`Fetch error for ${endpoint}:`, error);
                    const userFriendlyError = `Erro de comunicação com o servidor. <br><br> Verifique se a sua API em <strong>${API_BASE_URL}</strong> está a correr e configurada para CORS.`;
                    showError(userFriendlyError);
                    return null;
                }
            }
            
            async function loadInitialData() {
                showLoader(true);
                const [topicsResponse, notesResponse] = await Promise.all([ fetchAPI('/topic'), fetchAPI('/notes') ]);

                studyData = {};
                if (topicsResponse?.data) {
                    topicsResponse.data.forEach(topic => { studyData[topic.id] = { name: topic.name, notes: {} }; });
                }
                if (notesResponse?.data) {
                    notesResponse.data.forEach(note => {
                        if (note.topico_anotacao_id && studyData[note.topico_anotacao_id]) {
                            studyData[note.topico_anotacao_id].notes[note.id] = { 
                            title: note.name, 
                            content: note.content,
                            file_path: note.file_path,
                            file_type: note.file_type
                        };
                        }
                    });
                }
                showLoader(false);
                renderTopics();
            }

            function renderTopics() {
                if (!topicListEl) return;
                topicListEl.innerHTML = '';
                if (Object.keys(studyData).length === 0 && (!loaderEl || loaderEl.style.display === 'none')) {
                     topicListEl.innerHTML = `<p class="text-sm text-neutral-500 text-center mt-4">Nenhum tópico criado.</p>`;
                     return;
                }

                Object.entries(studyData).forEach(([topicId, topic]) => {
                    const notesArray = Object.entries(topic.notes);
                    const notesHtml = notesArray.length > 0
                        ? notesArray.map(([noteId, note]) => `
                            <li class="note-item flex justify-between items-center p-2 rounded-lg cursor-pointer text-neutral-400 ${noteId == activeNoteId ? 'bg-neutral-700 text-white' : 'hover:bg-neutral-700/50'}" data-note-id="${noteId}" data-topic-id="${topicId}">
                                <span class="truncate"><i class="fas fa-file-alt mr-2"></i>${note.title}</span>
                            </li>`).join('')
                        : `<li class="text-xs text-neutral-500 italic p-2">Não há anotações neste tópico.</li>`;

                    const topicDiv = document.createElement('div');
                    topicDiv.className = 'topic-container mb-2';
                    topicDiv.innerHTML = `
                        <div class="folder-header flex justify-between items-center p-2 rounded-lg cursor-pointer ${topicId == activeTopicId ? 'bg-red-600/20 text-red-400' : 'hover:bg-neutral-700'}" data-topic-id="${topicId}">
                            <span class="font-bold truncate"><i class="fas fa-folder mr-2"></i>${topic.name}</span>
                            <div class="flex items-center">
                                <button class="delete-topic-btn text-xs h-6 w-6 rounded hover:bg-neutral-600 text-neutral-500 hover:text-red-400" title="Eliminar Tópico" data-topic-id="${topicId}"><i class="fas fa-trash-alt"></i></button>
                                <button class="add-note-btn text-xs h-6 w-6 rounded hover:bg-neutral-600" title="Adicionar Anotação" data-topic-id="${topicId}"><i class="fas fa-plus"></i></button>
                            </div>
                        </div>
                        <ul class="notes-list pl-4 mt-1 space-y-1 ${topicId == activeTopicId ? '' : 'hidden'}">${notesHtml}</ul>`;
                    topicListEl.appendChild(topicDiv);
                });
            }

            async function loadNoteIntoEditor(topicId, noteId) {
                // Busca a nota completa da API para ter informações do arquivo
                const response = await fetchAPI(`/notes/${noteId}`);
                const fullNote = response?.anotacao || studyData[topicId]?.notes[noteId];
                if (!fullNote) return;
                
                noteTitleInput.value = fullNote.name || fullNote.title;
                
                // Inicializa o editor se ainda não foi inicializado
                if (!quillEditor) {
                    initQuillEditor();
                }
                
                // Carrega o conteúdo no Quill
                if (quillEditor) {
                    quillEditor.root.innerHTML = fullNote.content || '';
                } else {
                    noteContentArea.innerHTML = fullNote.content || '';
                }
                
                // Mostra informações do arquivo se existir
                const fileInfoEl = document.getElementById('file-info');
                if (fullNote.file_path) {
                    const fileName = fullNote.file_path.split('/').pop();
                    fileInfoEl.classList.remove('hidden');
                    fileInfoEl.innerHTML = `
                        <i class="fas fa-file mr-2"></i>
                        <span>Arquivo anexado: ${fileName}</span>
                        <a href="/storage/${fullNote.file_path}" target="_blank" class="ml-2 text-blue-400 hover:text-blue-300">
                            <i class="fas fa-download"></i> Baixar
                        </a>
                    `;
                } else {
                    fileInfoEl.classList.add('hidden');
                }
                
                // Limpa seleção de novo arquivo
                currentFile = null;
                if (noteFileInput) noteFileInput.value = '';
                
                showEditor(true);
            }

            function showLoader(isLoading) {
                 if (loaderEl) loaderEl.style.display = isLoading ? 'block' : 'none';
                 if (!isLoading && topicListEl) topicListEl.innerHTML = '';
            }
            
            function showError(message) {
                 if (loaderEl) loaderEl.style.display = 'none';
                 if (topicListEl) topicListEl.innerHTML = `<div class="text-sm text-red-500 text-center mt-4 p-4">${message}</div>`;
            }

            function openTopicModal(mode = 'add', topicId = null, currentName = '') {
                topicIdInput.value = topicId;
                topicModalTitle.textContent = mode === 'edit' ? 'Editar Tópico' : 'Novo Tópico';
                topicNameInput.value = currentName;
                topicModal.classList.add('active');
                topicNameInput.focus();
            }

            topicListEl?.addEventListener('click', async (e) => {
                const folderHeader = e.target.closest('.folder-header');
                const noteItem = e.target.closest('.note-item');
                const addNoteBtn = e.target.closest('.add-note-btn');
                const deleteTopicBtn = e.target.closest('.delete-topic-btn');

                if (deleteTopicBtn) {
                    e.stopPropagation();
                    const topicId = deleteTopicBtn.dataset.topicId;
                    if (confirm(`Tem a certeza que quer eliminar o tópico "${studyData[topicId].name}" e todas as suas anotações?`)) {
                        const response = await fetchAPI(`/topic/${topicId}`, { method: 'DELETE' });
                        if(response?.status) {
                           delete studyData[topicId];
                           if(activeTopicId === topicId) { activeTopicId = null; activeNoteId = null; showEditor(false); }
                           renderTopics();
                        }
                    }
                } else if (addNoteBtn) {
                    e.stopPropagation();
                    const topicId = addNoteBtn.dataset.topicId;
                    activeTopicId = topicId;
                    activeNoteId = 'new';
                    renderTopics();
                    noteTitleInput.value = '';
                    noteContentArea.innerHTML = '';
                    showEditor(true);
                    noteTitleInput.focus();
                } else if (folderHeader) {
                    const clickedTopicId = folderHeader.dataset.topicId;
                    activeTopicId = (activeTopicId == clickedTopicId) ? null : clickedTopicId;
                    activeNoteId = null;
                    showEditor(false);
                    renderTopics();
                } else if (noteItem) {
                    const { noteId, topicId } = noteItem.dataset;
                    activeNoteId = noteId;
                    activeTopicId = topicId;
                    renderTopics();
                    loadNoteIntoEditor(topicId, noteId);
                }
            });

            topicListEl?.addEventListener('dblclick', (e) => {
                const folderHeader = e.target.closest('.folder-header');
                if (folderHeader) {
                    const topicId = folderHeader.dataset.topicId;
                    openTopicModal('edit', topicId, studyData[topicId].name);
                }
            });

            addTopicBtn?.addEventListener('click', () => openTopicModal('add'));
            topicModal?.querySelector('.close-button').addEventListener('click', () => topicModal.classList.remove('active'));
            
            topicForm?.addEventListener('submit', async (e) => {
                e.preventDefault();
                const topicId = topicIdInput.value;
                const newName = topicNameInput.value.trim();
                if (!newName) return;

                if (topicId) { // Edit Mode
                    const response = await fetchAPI(`/topic/${topicId}`, { method: 'PUT', body: JSON.stringify({ name: newName }) });
                    if(response?.user) { studyData[topicId].name = response.user.name; }
                } else { // Add Mode
                    const response = await fetchAPI('/topic', { method: 'POST', body: JSON.stringify({ name: newName }) });
                    if(response?.user) {
                       studyData[response.user.id] = { name: response.user.name, notes: {} };
                       activeTopicId = response.user.id;
                    }
                }
                renderTopics();
                topicModal.classList.remove('active');
            });

            // Gerenciar upload de arquivo
            const noteFileInput = document.getElementById('note-file-input');
            const fileInfo = document.getElementById('file-info');
            let currentFile = null;

            noteFileInput?.addEventListener('change', (e) => {
                const file = e.target.files[0];
                if (file) {
                    currentFile = file;
                    fileInfo.classList.remove('hidden');
                    fileInfo.innerHTML = `
                        <i class="fas fa-file mr-2"></i>
                        <span>${file.name}</span>
                        <button type="button" id="remove-file-btn" class="ml-2 text-red-400 hover:text-red-300">
                            <i class="fas fa-times"></i>
                        </button>
                    `;
                    
                    document.getElementById('remove-file-btn')?.addEventListener('click', () => {
                        noteFileInput.value = '';
                        currentFile = null;
                        fileInfo.classList.add('hidden');
                    });
                }
            });

            saveNoteBtn?.addEventListener('click', async () => {
                if (!activeTopicId) return;

                const isCreating = activeNoteId === 'new';
                const endpoint = isCreating ? '/api/notes' : `/api/notes/${activeNoteId}`;
                const method = isCreating ? 'POST' : 'PUT';
                
                // Pega o conteúdo do Quill se disponível, senão usa o conteúdo HTML normal
                let content = '';
                if (quillEditor) {
                    content = quillEditor.root.innerHTML;
                } else {
                    content = noteContentArea.innerHTML;
                }
                
                // Cria FormData para suportar upload de arquivo
                const formData = new FormData();
                formData.append('name', noteTitleInput.value || "Sem Título");
                formData.append('content', content);
                formData.append('topico_anotacao_id', activeTopicId);
                
                if (currentFile) {
                    formData.append('file', currentFile);
                }

                try {
                    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
                    const response = await fetch(endpoint, {
                        method: method,
                        credentials: 'include',
                        body: formData,
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest',
                            'X-CSRF-TOKEN': csrfToken || ''
                        }
                    });
                    
                    if (!response.ok) {
                        const errorData = await response.json().catch(() => ({ message: 'Erro ao salvar anotação' }));
                        alert('Erro ao salvar: ' + (errorData.message || errorData.error || 'Erro desconhecido'));
                        console.error('Erro na resposta:', errorData);
                        return;
                    }
                    
                    const result = await response.json();
                    
                    if (result?.anotacao || result?.status) {
                        const note = result.anotacao || result.data || result.note;
                        if (note) {
                            studyData[activeTopicId].notes[note.id] = { 
                                title: note.name, 
                                content: note.content,
                                file_path: note.file_path,
                                file_type: note.file_type
                            };
                            activeNoteId = note.id;
                            renderTopics();
                            
                            // Limpa o arquivo selecionado
                            currentFile = null;
                            if (noteFileInput) noteFileInput.value = '';
                            if (fileInfo) fileInfo.classList.add('hidden');
                            
                            alert(`Anotação ${isCreating ? 'criada' : 'guardada'} com sucesso!`);
                        }
                    } else {
                        alert('Erro ao salvar: ' + (result.message || 'Erro desconhecido'));
                    }
                } catch (error) {
                    console.error('Erro:', error);
                    alert('Erro ao salvar anotação');
                }
            });
            
            deleteNoteBtn?.addEventListener('click', async () => {
                if (activeTopicId && activeNoteId && activeNoteId !== 'new' && confirm('Tem a certeza que quer eliminar esta anotação?')) {
                     const response = await fetchAPI(`/notes/${activeNoteId}`, { method: 'DELETE' });
                     if(response?.status) {
                        delete studyData[activeTopicId].notes[activeNoteId];
                        activeNoteId = null;
                        showEditor(false);
                        renderTopics();
                        alert('Anotação eliminada.');
                     }
                }
            });

            // Inicializa o editor quando necessário
            function showEditor(show) {
                if (show) {
                    noteEditorEl?.classList.remove('hidden');
                    placeholderViewEl?.classList.add('hidden');
                    if (!quillEditor && noteContentArea) {
                        initQuillEditor();
                    }
                } else {
                    noteEditorEl?.classList.add('hidden');
                    placeholderViewEl?.classList.remove('hidden');
                }
            }

            loadInitialData();
        });
</script>
@endpush

