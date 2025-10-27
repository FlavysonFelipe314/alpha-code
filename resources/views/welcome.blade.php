<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>AlphaCode - Anotações</title>
    <!-- Incluindo o Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- Incluindo a biblioteca de ícones Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Orbitron:wght@400..900&family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap" rel="stylesheet">
    
    
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;500;700;900&display=swap');
        body {
            font-family: 'Inter', sans-serif;
            background-color: #0A0A0A;
            color: #E0E0E0;
            overflow: hidden;
        }
        .immersive-background {
            position: fixed; top: 0; left: 0; width: 100%; height: 100%;
            background: radial-gradient(circle at 50% 50%, rgba(176, 26, 26, 0.15) 0%, rgba(10,10,10,0) 60%);
            animation: pulse-background 10s infinite ease-in-out;
            z-index: -1;
        }
        @keyframes pulse-background {
            0%, 100% { transform: scale(1); opacity: 0.8; }
            50% { transform: scale(1.2); opacity: 1; }
        }
        ::-webkit-scrollbar { width: 8px; }
        ::-webkit-scrollbar-track { background: #18181b; }
        ::-webkit-scrollbar-thumb { background: #ef4444; border-radius: 4px; }
        ::-webkit-scrollbar-thumb:hover { background: #dc2626; }
        #note-content:focus { outline: none; }
        #editor-toolbar button {
            color: #a1a1aa; width: 2.5rem; height: 2.5rem;
            border-radius: 0.375rem; transition: background-color 0.2s, color 0.2s;
        }
        #editor-toolbar button:hover { background-color: #3f3f46; color: #fff; }
        #editor-toolbar button.active { background-color: #ef4444; color: #fff; }
        .modal {
            position: fixed; top: 0; left: 0; width: 100%; height: 100%;
            display: flex; align-items: center; justify-content: center;
            background-color: rgba(0, 0, 0, 0.8); backdrop-filter: blur(8px);
            z-index: 100; opacity: 0; visibility: hidden; transition: opacity 0.3s, visibility 0.3s;
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
</head>
<body class="min-h-screen">
    <div class="immersive-background"></div>

    <main class="h-screen w-screen flex flex-col p-4 md:p-6 lg:p-8">
        <header class="flex items-center space-x-4 pb-4 border-b border-neutral-800 flex-shrink-0">
            <img src="logo.png" alt="Logo" class="h-10 w-auto" style="object-fit: cover; width:100px;">
            <h1 class="text-2xl font-black text-white uppercase tracking-wider">Bloco de notas</h1>
        </header>

        <div class="flex-grow flex gap-6 mt-6 overflow-hidden">
            <!-- Coluna Esquerda: Tópicos e Anotações -->
            <div id="sidebar" class="w-1/3 flex flex-col bg-black/30 rounded-lg p-4">
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
            <div id="editor-container" class="w-2/3 flex flex-col bg-neutral-900/50 rounded-lg">
                <div id="note-editor" class="flex-grow flex flex-col p-6 hidden">
                    <input type="text" id="note-title" placeholder="Título da Anotação..." class="bg-transparent text-2xl font-bold text-white mb-4 focus:outline-none flex-shrink-0">
                    <div id="editor-toolbar" class="flex items-center space-x-1 mb-2 border-y border-neutral-700 py-2 flex-shrink-0">
                        <button data-command="bold" title="Negrito"><i class="fas fa-bold"></i></button>
                        <button data-command="italic" title="Itálico"><i class="fas fa-italic"></i></button>
                        <button data-command="underline" title="Sublinhado"><i class="fas fa-underline"></i></button>
                        <button data-command="insertUnorderedList" title="Lista não ordenada"><i class="fas fa-list-ul"></i></button>
                        <button data-command="insertOrderedList" title="Lista ordenada"><i class="fas fa-list-ol"></i></button>
                        <button data-command="formatBlock" data-value="h2" title="Cabeçalho"><i class="fas fa-heading"></i></button>
                    </div>
                    <div id="note-content" contenteditable="true" class="flex-grow text-neutral-300 overflow-y-auto py-4"></div>
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
    </main>

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
    
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            
            const API_BASE_URL = 'http://localhost:8000/api';
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
            const editorToolbar = document.getElementById('editor-toolbar');
            const loaderEl = document.getElementById('study-guide-loader');
            const topicModal = document.getElementById('topic-modal');
            const topicForm = document.getElementById('topic-form');
            const topicModalTitle = document.getElementById('topic-modal-title');
            const topicNameInput = document.getElementById('topic-name-input');
            const topicIdInput = document.getElementById('topic-id-input');

            async function fetchAPI(endpoint, options = {}) {
                try {
                    const response = await fetch(`${API_BASE_URL}${endpoint}`, {
                        headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', ...options.headers },
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
                            studyData[note.topico_anotacao_id].notes[note.id] = { title: note.name, content: note.content };
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

            function showEditor(show) {
                noteEditorEl?.classList.toggle('hidden', !show);
                placeholderViewEl?.classList.toggle('hidden', show);
            }

            function loadNoteIntoEditor(topicId, noteId) {
                const note = studyData[topicId].notes[noteId];
                noteTitleInput.value = note.title;
                noteContentArea.innerHTML = note.content;
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

            saveNoteBtn?.addEventListener('click', async () => {
                if (!activeTopicId) return;

                const isCreating = activeNoteId === 'new';
                const endpoint = isCreating ? '/notes' : `/notes/${activeNoteId}`;
                const method = isCreating ? 'POST' : 'PUT';
                const data = {
                    name: noteTitleInput.value || "Sem Título",
                    content: noteContentArea.innerHTML,
                    topico_anotacao_id: activeTopicId
                };

                const response = await fetchAPI(endpoint, { method, body: JSON.stringify(data) });

                if (response?.anotacao) {
                    const note = response.anotacao;
                    studyData[activeTopicId].notes[note.id] = { title: note.name, content: note.content };
                    activeNoteId = note.id;
                    renderTopics();
                    alert(`Anotação ${isCreating ? 'criada' : 'guardada'} com sucesso!`);
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

            editorToolbar?.addEventListener('click', (e) => {
                const button = e.target.closest('button');
                if (!button) return;
                document.execCommand(button.dataset.command, false, button.dataset.value);
                noteContentArea?.focus();
            });

            loadInitialData();
        });
    </script>
</body>
</html>

