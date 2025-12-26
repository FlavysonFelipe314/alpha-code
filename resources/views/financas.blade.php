@extends('layouts.app')

@section('title', 'Finanças')

@push('styles')
<style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;500;700;900&display=swap');
        /* Estilos específicos da página de finanças */
        @keyframes pulse-background {
            0%, 100% { transform: scale(1); opacity: 0.7; }
            50% { transform: scale(1.1); opacity: 0.9; }
        }
        ::-webkit-scrollbar { width: 8px; height: 8px; }
        ::-webkit-scrollbar-track { background: #1f2937; border-radius: 4px;}
        ::-webkit-scrollbar-thumb { background: #ef4444; border-radius: 4px; }
        ::-webkit-scrollbar-thumb:hover { background: #dc2626; }
        .modal {
            position: fixed; top: 0; left: 0; width: 100%; height: 100%;
            display: flex; align-items: center; justify-content: center;
            background-color: rgba(0, 0, 0, 0.85); backdrop-filter: blur(10px);
            z-index: 1000; opacity: 0; visibility: hidden; transition: opacity 0.3s, visibility 0.3s;
        }
        .modal.active { opacity: 1; visibility: visible; }
        .modal-content {
            background: rgba(31, 41, 55, 0.9); /* More transparent background */
            border: 1px solid rgba(75, 85, 99, 0.5); /* Lighter border */
            border-radius: 12px;
            padding: 2rem; width: 90%; max-width: 550px;
            transform: scale(0.95); transition: transform 0.3s; position: relative;
            box-shadow: 0 20px 25px -5px rgb(0 0 0 / 0.4), 0 8px 10px -6px rgb(0 0 0 / 0.4);
            backdrop-filter: blur(8px); /* Add blur to modal */
        }
        .modal.active .modal-content { transform: scale(1); }
        .close-button {
            position: absolute; top: 1rem; right: 1rem; background: none; border: none;
            color: #9ca3af; font-size: 1.5rem; cursor: pointer; transition: color 0.3s;
        }
        .close-button:hover { color: white; }
        .form-input, .form-select {
            background-color: rgba(55, 65, 81, 0.7); /* Transparent input background */
            border: 1px solid rgba(75, 85, 99, 0.5); /* Transparent border */
            border-radius: 0.5rem;
            padding: 0.75rem 1rem; color: white; transition: border-color 0.2s, box-shadow 0.2s; appearance: none;
            backdrop-filter: blur(5px); /* Add blur to inputs */
        }
        /* Adiciona seta para selects */
        .form-select {
             background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 20 20'%3e%3cpath stroke='%239ca3af' stroke-linecap='round' stroke-linejoin='round' stroke-width='1.5' d='M6 8l4 4 4-4'/%3e%3c/svg%3e");
             background-position: right 0.5rem center;
             background-repeat: no-repeat;
             background-size: 1.5em 1.5em;
             padding-right: 2.5rem;
        }
        .form-input:focus, .form-select:focus { outline: none; border-color: #ef4444; box-shadow: 0 0 0 2px rgba(239, 68, 68, 0.5); }
        .widget-card {
            background: rgba(31, 41, 55, 0.5); /* More transparent background for cards */
            border: 1px solid rgba(75, 85, 99, 0.5); /* Lighter border */
            border-radius: 0.75rem;
            backdrop-filter: blur(10px); /* Add blur effect to cards */
            -webkit-backdrop-filter: blur(10px);
        }
        .nav-link {
            display: flex; align-items: center; justify-content: center; /* Center icons for top bar */
            padding: 0.75rem 1rem; border-radius: 0.5rem;
            color: #9ca3af; transition: background-color 0.2s, color 0.2s; 
            font-weight: 500; /* Slightly bolder text */
        }
        .nav-link:hover { background-color: rgba(55, 65, 81, 0.7); color: white; }
        .nav-link.active { color: white; font-weight: 600; background-color: rgba(239, 68, 68, 0.2); }
        .nav-link i { margin-right: 0.5rem; } /* Add margin for text */

        /* Override for top navigation - no text for smaller screens, only icon */
        @media (max-width: 768px) {
            .nav-link span {
                display: none;
            }
            .nav-link {
                padding: 0.75rem; /* Smaller padding */
            }
            .nav-link i {
                margin-right: 0; /* No margin */
            }
        }
        
        .action-button { background-color: rgba(55, 65, 81, 0.7); color: #d1d5db; transition: background-color 0.2s, color 0.2s; }
        .action-button:hover { background-color: rgba(75, 85, 99, 0.7); color: white; }
        .action-button-danger:hover { background-color: #ef4444; color: white; }
        .chart-container { position: relative; }
        .chart-loader { position: absolute; inset: 0; display: flex; align-items: center; justify-content: center; background-color: rgba(31, 41, 55, 0.7); z-index: 10; border-radius: 0.75rem; }
        .list-item-hover-buttons .action-buttons {
            opacity: 0; transition: opacity 0.2s;
        }
        .list-item-hover-buttons:hover .action-buttons {
            opacity: 1;
        }
        .tab-button {
            transition: background-color 0.2s, color 0.2s;
        }
        .tab-button.active {
            background-color: #ef4444;
            color: white;
        }
    </style>
@endpush

@section('content')
<div class="container mx-auto p-4 md:p-6 lg:p-8">
    <!-- Internal Navigation Bar -->
    <header class="mb-6 p-4 bg-neutral-900/50 rounded-lg flex flex-col md:flex-row justify-between items-center border border-neutral-700/50 backdrop-filter backdrop-blur-lg">
        <h1 class="text-xl font-black text-white uppercase tracking-wider mb-4 md:mb-0">Financeiro</h1>
        <nav class="flex space-x-2 mb-4 md:mb-0">
            <a href="#" class="nav-link active" data-view="dashboard"><i class="fas fa-tachometer-alt"></i><span>Dashboard</span></a>
            <a href="#" class="nav-link" data-view="receitas"><i class="fas fa-chart-pie"></i><span>Receitas</span></a>
            <a href="#" class="nav-link" data-view="custos"><i class="fas fa-chart-line"></i><span>Custos</span></a>
            <a href="#" class="nav-link" data-view="carteira"><i class="fas fa-wallet"></i><span>Carteira</span></a>
        </nav>
        <div class="flex items-center space-x-2">
            <button id="add-receita-btn" class="p-2 bg-green-600 hover:bg-green-700 text-white rounded-lg transition flex items-center justify-center text-sm" title="Nova Receita">
                <i class="fas fa-plus"></i><span class="ml-2">Receita</span>
            </button>
            <button id="add-custo-btn" class="p-2 bg-red-600 hover:bg-red-700 text-white rounded-lg transition flex items-center justify-center text-sm" title="Novo Custo">
                <i class="fas fa-minus"></i><span class="ml-2">Custo</span>
            </button>
        </div>
    </header>
        
        <!-- View: Dashboard --><div id="view-dashboard" class="view-content">
            <header class="flex flex-col md:flex-row justify-between items-center mb-8">
                <div>
                     <h2 class="text-2xl font-bold text-white">Dashboard Financeiro</h2>
                     <p class="text-sm text-neutral-400" id="mes-atual-label">Carregando...</p>
                </div>

            </header>
             <section class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
                <div class="widget-card p-6"> <p class="text-sm font-medium text-neutral-400 mb-1">Saldo Total</p> <p class="text-3xl font-black text-white" id="saldo-total">R$ 0,00</p> </div>
                <div class="widget-card p-6"> <p class="text-sm font-medium text-neutral-400 mb-1">Receitas (Mês)</p> <p class="text-3xl font-black text-green-500" id="receitas-mes">R$ 0,00</p> </div>
                <div class="widget-card p-6"> <p class="text-sm font-medium text-neutral-400 mb-1">Custos (Mês)</p> <p class="text-3xl font-black text-red-500" id="custos-mes">R$ 0,00</p> </div>
                <div class="widget-card p-6"> <p class="text-sm font-medium text-neutral-400 mb-1">Balanço (Mês)</p> <p class="text-3xl font-black" id="balanco-mes">R$ 0,00</p> </div>
            </section>
             <section class="grid grid-cols-1 lg:grid-cols-5 gap-6 mb-8">
                 <div class="widget-card p-6 lg:col-span-3"> <h3 class="text-lg font-bold text-white mb-4">Evolução do Saldo</h3> <div class="h-64 chart-container"> <canvas id="balanceChart"></canvas> <div id="balanceChartLoader" class="chart-loader"><i class="fas fa-spinner fa-spin text-red-500 text-2xl"></i></div> </div> </div>
                 <div class="widget-card p-6 lg:col-span-2"> <h3 class="text-lg font-bold text-white mb-4">Despesas por Categoria</h3> <div class="h-64 chart-container"> <canvas id="expenseChart"></canvas> <div id="expenseChartLoader" class="chart-loader"><i class="fas fa-spinner fa-spin text-red-500 text-2xl"></i></div> </div> </div>
             </section>
             <!-- Adicionando um card para Receitas por Categoria para consistência visual --><section class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">
                <div class="widget-card p-6 lg:col-span-1"> <h3 class="text-lg font-bold text-white mb-4">Receitas por Categoria</h3> <div class="h-48 chart-container"> <canvas id="incomeChart"></canvas> <div id="incomeChartLoader" class="chart-loader"><i class="fas fa-spinner fa-spin text-red-500 text-2xl"></i></div> </div> </div>
                <div class="lg:col-span-2 space-y-6">
                    <div class="widget-card p-6"> <div class="flex justify-between items-center mb-4"> <h3 class="font-semibold text-neutral-300">Contas</h3> <button id="add-conta-btn" class="h-8 w-8 rounded-lg text-neutral-400 hover:bg-neutral-700 hover:text-white transition"><i class="fas fa-plus"></i></button> </div> <div id="contas-list" class="space-y-3 max-h-40 overflow-y-auto"></div> </div>
                    <div class="widget-card p-6"> <div class="flex justify-between items-center mb-4"> <h3 class="font-semibold text-neutral-300">Criptoativos</h3> <button id="add-cripto-btn" class="h-8 w-8 rounded-lg text-neutral-400 hover:bg-neutral-700 hover:text-white transition"><i class="fas fa-plus"></i></button> </div> <div id="criptos-list" class="space-y-3 max-h-40 overflow-y-auto"></div> </div>
                </div>
             </section>
             <section class="grid grid-cols-1">
                 <div class="widget-card p-6">
                     <h3 class="text-lg font-bold text-white mb-4">Transações Recentes</h3>
                     <div class="overflow-y-auto max-h-96">
                         <table class="w-full text-sm text-left text-neutral-400">
                             <thead class="text-xs text-neutral-500 uppercase sticky top-0 bg-gray-800/80 backdrop-blur-sm"> <tr> <th scope="col" class="py-3 px-4">Data</th> <th scope="col" class="py-3 px-4">Título</th> <th scope="col" class="py-3 px-4">Categoria</th> <th scope="col" class="py-3 px-4 text-right">Valor</th> <th scope="col" class="py-3 px-4 text-center">Status / Ações</th> </tr> </thead>
                             <tbody id="transactions-table-body"></tbody>
                         </table>
                     </div>
                 </div>
             </section>
        </div>
        
        <!-- View: Receitas --><div id="view-receitas" class="view-content hidden">

        <div class="flex justify-between items-center mb-4">
            <h2 class="text-2xl font-bold text-white mb-6">Minhas Receitas</h2>
            <button class="p-2 bg-green-600 hover:bg-green-700 text-white rounded-lg transition text-sm flex items-center justify-center" onclick="openTransactionModal('receita')">
                <i class="fas fa-plus mr-2"></i>Nova Receita
            </button>
        </div>
            <div class="widget-card p-6">
                <div class="overflow-y-auto max-h-96">
                    <table class="w-full text-sm text-left text-neutral-400">
                        <thead class="text-xs text-neutral-500 uppercase sticky top-0 bg-gray-800/80 backdrop-blur-sm"> 
                            <tr> 
                                <th scope="col" class="py-3 px-4">Data</th> 
                                <th scope="col" class="py-3 px-4">Título</th> 
                                <th scope="col" class="py-3 px-4">Categoria</th> 
                                <th scope="col" class="py-3 px-4 text-right">Valor</th> 
                                <th scope="col" class="py-3 px-4 text-center">Status / Ações</th> 
                            </tr> 
                        </thead>
                        <tbody id="receitas-table-body">
                            <!-- Conteúdo preenchido via JS ou com dados de exemplo --><tr class="border-b border-neutral-800 hover:bg-neutral-800/30">
                                <td class="py-3 px-4">01/05/2024</td>
                                <td class="py-3 px-4 text-white font-medium">Salário</td>
                                <td class="py-3 px-4">Salário</td>
                                <td class="py-3 px-4 text-right text-green-500 font-semibold">+ R$ 3.000,00</td>
                                <td class="py-3 px-4 text-center"><i class="fas fa-check-circle text-green-500" title="Efetivado"></i></td>
                            </tr>
                            <tr class="border-b border-neutral-800 hover:bg-neutral-800/30">
                                <td class="py-3 px-4">05/05/2024</td>
                                <td class="py-3 px-4 text-white font-medium">Freelance Projeto X</td>
                                <td class="py-3 px-4">Freelance</td>
                                <td class="py-3 px-4 text-right text-green-500 font-semibold">+ R$ 1.000,00</td>
                                <td class="py-3 px-4 text-center"><i class="fas fa-check-circle text-green-500" title="Efetivado"></i></td>
                            </tr>
                            <tr class="border-b border-neutral-800 hover:bg-neutral-800/30">
                                <td class="py-3 px-4">15/05/2024</td>
                                <td class="py-3 px-4 text-white font-medium">Venda Item Usado</td>
                                <td class="py-3 px-4">Vendas</td>
                                <td class="py-3 px-4 text-right text-green-500 font-semibold">+ R$ 200,00</td>
                                <td class="py-3 px-4 text-center"><i class="fas fa-check-circle text-green-500" title="Efetivado"></i></td>
                            </tr>
                            <tr class="border-b border-neutral-800 hover:bg-neutral-800/30">
                                <td class="py-3 px-4">20/05/2024</td>
                                <td class="py-3 px-4 text-white font-medium">Dividendo Ações</td>
                                <td class="py-3 px-4">Investimentos</td>
                                <td class="py-3 px-4 text-right text-green-500 font-semibold">+ R$ 300,00</td>
                                <td class="py-3 px-4 text-center"><i class="fas fa-hourglass-half text-yellow-500" title="Pendente"></i></td>
                            </tr>
                            <tr class="border-b border-neutral-800 hover:bg-neutral-800/30">
                                <td class="py-3 px-4">25/05/2024</td>
                                <td class="py-3 px-4 text-white font-medium">Reembolso</td>
                                <td class="py-3 px-4">Outros</td>
                                <td class="py-3 px-4 text-right text-green-500 font-semibold">+ R$ 100,00</td>
                                <td class="py-3 px-4 text-center"><i class="fas fa-hourglass-half text-yellow-500" title="Pendente"></i></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- View: Custos --><div id="view-custos" class="view-content hidden">
                <div class="flex justify-between items-center mb-4">
                                <h2 class="text-2xl font-bold text-white mb-6">Meus Custos</h2>

                    <button class="p-2 bg-red-600 hover:bg-red-700 text-white rounded-lg transition text-sm flex items-center justify-center" onclick="openTransactionModal('custo')">
                        <i class="fas fa-minus mr-2"></i>Novo Custo
                    </button>
                </div>

            <div class="widget-card p-6">
                <h3 class="text-lg font-bold text-white mb-4">Todos os Custos</h3>
                <div class="overflow-y-auto max-h-96">
                    <table class="w-full text-sm text-left text-neutral-400">
                        <thead class="text-xs text-neutral-500 uppercase sticky top-0 bg-gray-800/80 backdrop-blur-sm"> 
                            <tr> 
                                <th scope="col" class="py-3 px-4">Data</th> 
                                <th scope="col" class="py-3 px-4">Título</th> 
                                <th scope="col" class="py-3 px-4">Categoria</th> 
                                <th scope="col" class="py-3 px-4 text-right">Valor</th> 
                                <th scope="col" class="py-3 px-4 text-center">Status / Ações</th> 
                            </tr> 
                        </thead>
                        <tbody id="custos-table-body">
                            <!-- Conteúdo preenchido via JS ou com dados de exemplo --><tr class="border-b border-neutral-800 hover:bg-neutral-800/30">
                                <td class="py-3 px-4">02/05/2024</td>
                                <td class="py-3 px-4 text-white font-medium">Aluguel</td>
                                <td class="py-3 px-4">Moradia</td>
                                <td class="py-3 px-4 text-right text-red-500 font-semibold">- R$ 1.500,00</td>
                                <td class="py-3 px-4 text-center"><i class="fas fa-check-circle text-green-500" title="Efetivado"></i></td>
                            </tr>
                            <tr class="border-b border-neutral-800 hover:bg-neutral-800/30">
                                <td class="py-3 px-4">03/05/2024</td>
                                <td class="py-3 px-4 text-white font-medium">Supermercado</td>
                                <td class="py-3 px-4">Alimentação</td>
                                <td class="py-3 px-4 text-right text-red-500 font-semibold">- R$ 300,00</td>
                                <td class="py-3 px-4 text-center"><i class="fas fa-check-circle text-green-500" title="Efetivado"></i></td>
                            </tr>
                            <tr class="border-b border-neutral-800 hover:bg-neutral-800/30">
                                <td class="py-3 px-4">10/05/2024</td>
                                <td class="py-3 px-4 text-white font-medium">Conta de Luz</td>
                                <td class="py-3 px-4">Moradia</td>
                                <td class="py-3 px-4 text-right text-red-500 font-semibold">- R$ 150,00</td>
                                <td class="py-3 px-4 text-center"><i class="fas fa-check-circle text-green-500" title="Efetivado"></i></td>
                            </tr>
                            <tr class="border-b border-neutral-800 hover:bg-neutral-800/30">
                                <td class="py-3 px-4">18/05/2024</td>
                                <td class="py-3 px-4 text-white font-medium">Internet</td>
                                <td class="py-3 px-4">Moradia</td>
                                <td class="py-3 px-4 text-right text-red-500 font-semibold">- R$ 100,00</td>
                                <td class="py-3 px-4 text-center"><i class="fas fa-hourglass-half text-yellow-500" title="Pendente"></i></td>
                            </tr>
                            <tr class="border-b border-neutral-800 hover:bg-neutral-800/30">
                                <td class="py-3 px-4">22/05/2024</td>
                                <td class="py-3 px-4 text-white font-medium">Assinatura Streaming</td>
                                <td class="py-3 px-4">Lazer</td>
                                <td class="py-3 px-4 text-right text-red-500 font-semibold">- R$ 50,00</td>
                                <td class="py-3 px-4 text-center"><i class="fas fa-hourglass-half text-yellow-500" title="Pendente"></i></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- View: Carteira --><div id="view-carteira" class="view-content hidden">
            <h2 class="text-2xl font-bold text-white mb-6">Minha Carteira</h2>
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <div class="widget-card p-6">
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="font-semibold text-neutral-300">Contas Bancárias</h3>
                        <button id="add-conta-btn-view" class="p-2 bg-red-600 hover:bg-red-700 text-white rounded-lg transition text-sm flex items-center justify-center" onclick="openContaModal()">
                            <i class="fas fa-plus mr-2"></i>Nova Conta
                        </button>
                    </div>
                    <div id="contas-list-view" class="space-y-3 max-h-80 overflow-y-auto">
                        <!-- Conteúdo preenchido via JS ou com dados de exemplo --><div class="list-item-hover-buttons flex justify-between items-center bg-neutral-800/50 p-3 rounded-md">
                            <div>
                                <p class="text-sm font-medium text-white">Banco Principal</p>
                                <p class="text-xs text-neutral-500">Corrente - Pessoal</p>
                            </div>
                            <div class="flex items-center space-x-2">
                                <span class="text-sm font-semibold text-white">R$ 4.500,00</span>
                                <div class="action-buttons">
                                    <button class="action-button h-6 w-6 rounded text-xs" title="Editar"><i class="fas fa-pencil-alt"></i></button>
                                    <button class="action-button action-button-danger h-6 w-6 rounded text-xs" title="Eliminar"><i class="fas fa-trash-alt"></i></button>
                                </div>
                            </div>
                        </div>
                        <div class="list-item-hover-buttons flex justify-between items-center bg-neutral-800/50 p-3 rounded-md">
                            <div>
                                <p class="text-sm font-medium text-white">Nubank</p>
                                <p class="text-xs text-neutral-500">Poupança - Pessoal</p>
                            </div>
                            <div class="flex items-center space-x-2">
                                <span class="text-sm font-semibold text-white">R$ 1.200,00</span>
                                <div class="action-buttons">
                                    <button class="action-button h-6 w-6 rounded text-xs" title="Editar"><i class="fas fa-pencil-alt"></i></button>
                                    <button class="action-button action-button-danger h-6 w-6 rounded text-xs" title="Eliminar"><i class="fas fa-trash-alt"></i></button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="widget-card p-6">
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="font-semibold text-neutral-300">Criptoativos</h3>
                        <button id="add-cripto-btn-view" class="p-2 bg-red-600 hover:bg-red-700 text-white rounded-lg transition text-sm flex items-center justify-center" onclick="openCriptoModal()">
                            <i class="fas fa-plus mr-2"></i>Novo Cripto
                        </button>
                    </div>
                    <div id="criptos-list-view" class="space-y-3 max-h-80 overflow-y-auto">
                        <!-- Conteúdo preenchido via JS ou com dados de exemplo --><div class="list-item-hover-buttons flex justify-between items-center bg-neutral-800/50 p-3 rounded-md">
                            <div>
                                <p class="text-sm font-medium text-white">Bitcoin (BTC)</p>
                                <p class="text-xs text-neutral-500">Carteira X</p>
                            </div>
                            <div class="flex items-center space-x-2">
                                <span class="text-sm font-semibold text-white">0.05 BTC</span>
                                <div class="action-buttons">
                                    <button class="action-button h-6 w-6 rounded text-xs" title="Editar"><i class="fas fa-pencil-alt"></i></button>
                                    <button class="action-button action-button-danger h-6 w-6 rounded text-xs" title="Eliminar"><i class="fas fa-trash-alt"></i></button>
                                </div>
                            </div>
                        </div>
                        <div class="list-item-hover-buttons flex justify-between items-center bg-neutral-800/50 p-3 rounded-md">
                            <div>
                                <p class="text-sm font-medium text-white">Ethereum (ETH)</p>
                                <p class="text-xs text-neutral-500">MetaMask</p>
                            </div>
                            <div class="flex items-center space-x-2">
                                <span class="text-sm font-semibold text-white">0.8 ETH</span>
                                <div class="action-buttons">
                                    <button class="action-button h-6 w-6 rounded text-xs" title="Editar"><i class="fas fa-pencil-alt"></i></button>
                                    <button class="action-button action-button-danger h-6 w-6 rounded text-xs" title="Eliminar"><i class="fas fa-trash-alt"></i></button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

</div>

<!-- Modais --><div id="transaction-modal" class="modal"> <div class="modal-content max-h-[90vh] flex flex-col"> <button class="close-button">&times;</button> <h2 id="transaction-modal-title" class="text-2xl font-bold text-white mb-6 flex-shrink-0">Nova Transação</h2> <form id="transaction-form" class="flex-grow overflow-y-auto pr-4 space-y-4"> <input type="hidden" id="transaction-type"> <input type="hidden" id="transaction-id"> <div><label for="transaction-titulo" class="block text-sm font-medium text-neutral-300 mb-1">Título</label><input type="text" id="transaction-titulo" placeholder="Ex: Salário, Supermercado" class="mt-1 w-full form-input" required></div> <div class="grid grid-cols-2 gap-4"> <div><label for="transaction-valor" class="block text-sm font-medium text-neutral-300 mb-1">Valor (R$)</label><input type="number" step="0.01" id="transaction-valor" placeholder="0,00" class="mt-1 w-full form-input" required></div> <div><label for="transaction-data" class="block text-sm font-medium text-neutral-300 mb-1">Data</label><input type="date" id="transaction-data" class="mt-1 w-full form-input" required></div> </div> <div> <label for="transaction-categoria" class="block text-sm font-medium text-neutral-300 mb-1">Categoria</label> <select id="transaction-categoria" class="mt-1 w-full form-select" required> <option value="" disabled selected>Selecione...</option> </select> </div> <div id="custo-fields" class="space-y-4 hidden"> <div class="grid grid-cols-2 gap-4"> <div> <label for="custo-tipo" class="block text-sm font-medium text-neutral-300 mb-1">Tipo</label> <select id="custo-tipo" class="mt-1 w-full form-select"></select> </div> <div> <label for="custo-forma-pagamento" class="block text-sm font-medium text-neutral-300 mb-1">Forma Pagamento</label> <select id="custo-forma-pagamento" class="mt-1 w-full form-select"></select> </div> </div> </div> <div><label for="transaction-observacao" class="block text-sm font-medium text-neutral-300 mb-1">Observação</label><textarea id="transaction-observacao" rows="2" class="mt-1 w-full form-input"></textarea></div> <div class="flex items-center"><input type="checkbox" id="transaction-efetivado" class="h-4 w-4 rounded border-gray-300 text-red-600 focus:ring-red-500 bg-neutral-700 border-neutral-600"><label for="transaction-efetivado" class="ml-2 block text-sm text-neutral-300">Marcar como Efetivado</label></div> <div class="pt-6 border-t border-neutral-700 flex-shrink-0"><button type="submit" class="w-full bg-red-600 hover:bg-red-700 text-white font-bold py-3 rounded-lg transition">Guardar Transação</button></div> </form> </div> </div>
    <div id="conta-modal" class="modal"> <div class="modal-content max-h-[90vh] flex flex-col"> <button class="close-button">&times;</button> <h2 id="conta-modal-title" class="text-2xl font-bold text-white mb-6 flex-shrink-0">Nova Conta Bancária</h2> <form id="conta-form" class="space-y-4"> <input type="hidden" id="conta-id"> <div><label for="conta-banco" class="block text-sm font-medium text-neutral-300 mb-1">Banco</label><input type="text" id="conta-banco" placeholder="Ex: Banco Principal" class="mt-1 w-full form-input" required></div> <div class="grid grid-cols-2 gap-4"> <div><label for="conta-saldo" class="block text-sm font-medium text-neutral-300 mb-1">Saldo Atual (R$)</label><input type="number" step="0.01" id="conta-saldo" placeholder="0,00" class="mt-1 w-full form-input" required></div> <div><label for="conta-tipo" class="block text-sm font-medium text-neutral-300 mb-1">Tipo de Conta</label><select id="conta-tipo" class="mt-1 w-full form-select"><option>Corrente</option><option>Poupança</option><option>Investimento</option><option>Pagamentos</option></select></div> </div> <div><label for="conta-pessoa" class="block text-sm font-medium text-neutral-300 mb-1">Pessoa</label><select id="conta-pessoa" class="mt-1 w-full form-select"><option>Pessoal</option><option>Empresa</option></select></div> <div><label for="conta-observacao" class="block text-sm font-medium text-neutral-300 mb-1">Observação</label><textarea id="conta-observacao" rows="2" class="mt-1 w-full form-input"></textarea></div> <div class="pt-6 border-t border-neutral-700 flex-shrink-0"><button type="submit" class="w-full bg-red-600 hover:bg-red-700 text-white font-bold py-3 rounded-lg transition">Guardar Conta</button></div> </form> </div> </div>
    <div id="cripto-modal" class="modal"> <div class="modal-content max-h-[90vh] flex flex-col"> <button class="close-button">&times;</button> <h2 id="cripto-modal-title" class="text-2xl font-bold text-white mb-6 flex-shrink-0">Novo Criptoativo</h2> <form id="cripto-form" class="space-y-4"> <input type="hidden" id="cripto-id"> <div class="grid grid-cols-2 gap-4"> <div><label for="cripto-moeda" class="block text-sm font-medium text-neutral-300 mb-1">Moeda</label><input type="text" id="cripto-moeda" placeholder="Ex: Bitcoin (BTC)" class="mt-1 w-full form-input" required></div> <div><label for="cripto-saldo" class="block text-sm font-medium text-neutral-300 mb-1">Saldo</label><input type="number" step="any" id="cripto-saldo" placeholder="0.0000" class="mt-1 w-full form-input" required></div> </div> <div><label for="cripto-observacao" class="block text-sm font-medium text-neutral-300 mb-1">Observação</label><textarea id="cripto-observacao" rows="2" class="mt-1 w-full form-input"></textarea></div> <div class="pt-6 border-t border-neutral-700 flex-shrink-0"><button type="submit" class="w-full bg-red-600 hover:bg-red-700 text-white font-bold py-3 rounded-lg transition">Guardar Criptoativo</button></div> </form> </div>     </div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', () => {
            
            const API_BASE_URL = `${window.location.origin}/api`; 

            // Elementos DOM (vários)...
            const addReceitaBtn = document.getElementById('add-receita-btn');
            const addCustoBtn = document.getElementById('add-custo-btn');
            const transactionModal = document.getElementById('transaction-modal');
            const transactionForm = document.getElementById('transaction-form');
            const transactionModalTitle = document.getElementById('transaction-modal-title');
            const transactionTypeInput = document.getElementById('transaction-type');
            const transactionIdInput = document.getElementById('transaction-id');
            const custoFields = document.getElementById('custo-fields');
            const addContaBtn = document.getElementById('add-conta-btn'); // Botão do dashboard
            const contaModal = document.getElementById('conta-modal');
            const contaForm = document.getElementById('conta-form');
            const contaModalTitle = document.getElementById('conta-modal-title');
            const contaIdInput = document.getElementById('conta-id');
            const addCriptoBtn = document.getElementById('add-cripto-btn'); // Botão do dashboard
            const criptoModal = document.getElementById('cripto-modal');
            const criptoForm = document.getElementById('cripto-form');
            const criptoModalTitle = document.getElementById('cripto-modal-title');
            const criptoIdInput = document.getElementById('cripto-id');
            const saldoTotalEl = document.getElementById('saldo-total');
            const receitasMesEl = document.getElementById('receitas-mes');
            const custosMesEl = document.getElementById('custos-mes');
            const balancoMesEl = document.getElementById('balanco-mes');
            const contasLisEl = document.getElementById('contas-list'); // Lista do Dashboard
            const criptosLisEl = document.getElementById('criptos-list'); // Lista do Dashboard
            const transactionsTableBody = document.getElementById('transactions-table-body');
            const mesAtualLabel = document.getElementById('mes-atual-label');
            const transactionCategoriaSelect = document.getElementById('transaction-categoria');
            const custoFormaPagamentoSelect = document.getElementById('custo-forma-pagamento');
            const custoTipoSelect = document.getElementById('custo-tipo');
            const navLinks = document.querySelectorAll('.nav-link');
            const viewContents = document.querySelectorAll('.view-content');

            // Elementos específicos das novas views (para preencher com dados reais se a API suportar)
            const receitasTableBody = document.getElementById('receitas-table-body');
            const custosTableBody = document.getElementById('custos-table-body');
            const contasListView = document.getElementById('contas-list-view');
            const criptosListView = document.getElementById('criptos-list-view');


            let balanceChartInstance = null;
            let expenseChartInstance = null;
            let incomeChartInstance = null; 

            // Opções Predefinidas para Selects
             const categoriasReceita = ['Salário', 'Freelance', 'Vendas', 'Investimentos', 'Outros'];
             const categoriasCusto = ['Alimentação', 'Transporte', 'Moradia', 'Lazer', 'Saúde', 'Educação', 'Vestuário', 'Impostos', 'Outros'];
             const tiposCusto = ['Fixo', 'Variável'];
             const formasPagamento = ['Cartão Crédito', 'Cartão Débito', 'Pix', 'Dinheiro', 'Transferência', 'Boleto'];


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
                        let errorData = { message: response.statusText }; 
                        try {
                             errorData = await response.json();
                        } catch (e) { /* Ignora erro de parse */ }
                        let errorMessages = errorData.message || response.statusText;
                        if(errorData.errors) { errorMessages += "\n" + Object.values(errorData.errors).flat().join("\n"); }
                        throw new Error(`Erro na API (${response.status}): ${errorMessages}`);
                    }
                    if (response.status === 204 || (response.status === 200 && options.method === 'DELETE')) return { status: true };
                    const text = await response.text();
                    return text ? JSON.parse(text) : { status: true }; 
                } catch (error) {
                    console.error(`Fetch error for ${endpoint}:`, error);
                    alert(`Erro: ${error.message}`);
                    return null;
                }
            }
            function formatCurrency(value) {
                if (isNaN(value)) return 'R$ 0,00';
                return value.toLocaleString('pt-BR', { style: 'currency', currency: 'BRL' });
            }

             function populateSelect(selectElement, options) {
                const previousValue = selectElement.value;
                selectElement.innerHTML = '<option value="" disabled selected>Selecione...</option>'; 
                options.forEach(option => {
                    const selected = option === previousValue ? ' selected' : '';
                    selectElement.innerHTML += `<option value="${option}"${selected}>${option}</option>`;
                });
            }

            function renderContas(contas = [], targetElement = contasLisEl) { // Adicionado targetElement para reutilização
                targetElement.innerHTML = '';
                if (contas.length === 0) {
                    targetElement.innerHTML = `<p class="text-sm text-neutral-500 text-center py-4">Nenhuma conta adicionada.</p>`; return;
                }
                contas.forEach(conta => {
                    const item = document.createElement('div');
                    item.className = 'list-item-hover-buttons flex justify-between items-center bg-neutral-800/50 p-3 rounded-md';
                    item.innerHTML = `
                        <div>
                            <p class="text-sm font-medium text-white">${conta.banco}</p>
                            <p class="text-xs text-neutral-500">${conta.tipo_conta || ''} - ${conta.pessoa || ''}</p>
                        </div>
                        <div class="flex items-center space-x-2">
                           <span class="text-sm font-semibold text-white">${formatCurrency(parseFloat(conta.saldo))}</span>
                           <div class="action-buttons">
                               <button class="edit-conta-btn action-button h-6 w-6 rounded text-xs" data-id="${conta.id}" title="Editar"><i class="fas fa-pencil-alt"></i></button>
                               <button class="delete-conta-btn action-button action-button-danger h-6 w-6 rounded text-xs" data-id="${conta.id}" title="Eliminar"><i class="fas fa-trash-alt"></i></button>
                           </div>
                        </div>`;
                    targetElement.appendChild(item);
                });
            }

             function renderCriptos(criptos = [], targetElement = criptosLisEl) { // Adicionado targetElement para reutilização
                 targetElement.innerHTML = '';
                 if (criptos.length === 0) {
                    targetElement.innerHTML = `<p class="text-sm text-neutral-500 text-center py-4">Nenhum criptoativo adicionado.</p>`; return;
                 }
                criptos.forEach(cripto => {
                    const item = document.createElement('div');
                     item.className = 'list-item-hover-buttons flex justify-between items-center bg-neutral-800/50 p-3 rounded-md';
                    item.innerHTML = `
                        <div>
                            <p class="text-sm font-medium text-white">${cripto.moeda}</p>
                            <p class="text-xs text-neutral-500">${cripto.observacao || ''}</p>
                        </div>
                         <div class="flex items-center space-x-2">
                            <span class="text-sm font-semibold text-white">${parseFloat(cripto.saldo)}</span>
                             <div class="action-buttons">
                               <button class="edit-cripto-btn action-button h-6 w-6 rounded text-xs" data-id="${cripto.id}" title="Editar"><i class="fas fa-pencil-alt"></i></button>
                               <button class="delete-cripto-btn action-button action-button-danger h-6 w-6 rounded text-xs" data-id="${cripto.id}" title="Eliminar"><i class="fas fa-trash-alt"></i></button>
                           </div>
                        </div>`;
                    targetElement.appendChild(item);
                });
            }

            function renderTransacoes(receitas = [], custos = [], targetElement = transactionsTableBody, limit = 10) { // Adicionado targetElement e limit
                targetElement.innerHTML = '';
                const transacoes = [
                    ...receitas.map(r => ({ ...r, type: 'receita', valor: parseFloat(r.valor), data: r.recebimento })),
                    ...custos.map(c => ({ ...c, type: 'custo', valor: parseFloat(c.custo), data: c.pagamento }))
                ].sort((a, b) => new Date(b.data) - new Date(a.data));

                if (transacoes.length === 0) {
                    targetElement.innerHTML = `<tr><td colspan="5" class="text-center py-6 text-neutral-500">Nenhuma transação registada.</td></tr>`; return;
                }

                transacoes.slice(0, limit).forEach(t => { 
                    const isReceita = t.type === 'receita';
                    const valorClass = isReceita ? 'text-green-500' : 'text-red-500';
                    const valorSignal = isReceita ? '+' : '-';
                    const efetivadoIcon = t.efetivado ? '<i class="fas fa-check-circle text-green-500" title="Efetivado"></i>' : '<i class="fas fa-hourglass-half text-yellow-500" title="Pendente"></i>';
                    
                    const dateObject = new Date(t.data + 'T00:00:00-03:00'); 
                    const formattedDate = !isNaN(dateObject) ? dateObject.toLocaleDateString('pt-BR') : 'Data Inválida';

                    const row = document.createElement('tr');
                    row.className = 'border-b border-neutral-800 hover:bg-neutral-800/30';
                    row.innerHTML = `
                        <td class="py-3 px-4">${formattedDate}</td>
                        <td class="py-3 px-4 text-white font-medium">${t.titulo}</td>
                        <td class="py-3 px-4">${t.categoria}</td>
                        <td class="py-3 px-4 text-right ${valorClass} font-semibold">${valorSignal} ${formatCurrency(t.valor)}</td>
                        <td class="py-3 px-4 text-center">
                            ${efetivadoIcon}
                            <button class="edit-transaction-btn action-button h-6 w-6 rounded text-xs ml-2" data-id="${t.id}" data-type="${t.type}" title="Editar"><i class="fas fa-pencil-alt"></i></button>
                            <button class="delete-transaction-btn action-button action-button-danger h-6 w-6 rounded text-xs" data-id="${t.id}" data-type="${t.type}" title="Eliminar"><i class="fas fa-trash-alt"></i></button>
                        </td>
                    `;
                    targetElement.appendChild(row);
                });
            }


            function calculateTotalsAndCharts(receitas = [], custos = [], contas = []) {
                const saldoTotalContas = contas.reduce((sum, conta) => sum + parseFloat(conta.saldo), 0);
                saldoTotalEl.textContent = formatCurrency(saldoTotalContas);

                const now = new Date();
                const currentMonth = now.getMonth();
                const currentYear = now.getFullYear();
                mesAtualLabel.textContent = now.toLocaleDateString('pt-BR', { month: 'long', year: 'numeric' });

                const receitasMes = receitas.filter(r => { const d = new Date(r.recebimento + 'T00:00:00-03:00'); return d.getMonth() === currentMonth && d.getFullYear() === currentYear; });
                const custosMes = custos.filter(c => { const d = new Date(c.pagamento + 'T00:00:00-03:00'); return d.getMonth() === currentMonth && d.getFullYear() === currentYear; });

                const totalReceitasMes = receitasMes.reduce((sum, r) => sum + parseFloat(r.valor), 0);
                const totalCustosMes = custosMes.reduce((sum, c) => sum + parseFloat(c.custo), 0);
                const balancoMes = totalReceitasMes - totalCustosMes;

                receitasMesEl.textContent = formatCurrency(totalReceitasMes);
                custosMesEl.textContent = formatCurrency(totalCustosMes);
                balancoMesEl.textContent = formatCurrency(balancoMes);
                balancoMesEl.className = `text-3xl font-black ${balancoMes >= 0 ? 'text-green-500' : 'text-red-500'}`;


                const expenseData = processCategoricalData(custosMes, 'custo', 'custo'); // Para despesas: cores de custo
                const incomeData = processCategoricalData(receitasMes, 'valor', 'receita'); // Para receitas: cores de receita
                const balanceData = processMonthlyBalance(receitas, custos);

                balanceChartInstance = updateChart(balanceChartInstance, balanceData, 'line', 'balanceChart', 'balanceChartLoader', { scales: { y: { beginAtZero: true, grid:{ color: '#3f3f46'}, ticks:{color: '#a1a1aa'} }, x:{ grid:{ color: '#3f3f46'}, ticks:{color: '#a1a1aa'} } }, plugins:{ legend:{ labels:{ color: '#a1a1aa'} } } });
                expenseChartInstance = updateChart(expenseChartInstance, expenseData, 'doughnut', 'expenseChart', 'expenseChartLoader', { plugins:{ legend:{ position: 'right', labels:{ color: '#a1a1aa'} } } });
                incomeChartInstance = updateChart(incomeChartInstance, incomeData, 'doughnut', 'incomeChart', 'incomeChartLoader', { plugins:{ legend:{ position: 'right', labels:{ color: '#a1a1aa'} } } }); // Gráfico de Receitas
            }
            
            function processCategoricalData(transactions, valueField, type) {
                 const categories = {};
                 transactions.forEach(t => { categories[t.categoria] = (categories[t.categoria] || 0) + parseFloat(t[valueField]); });
                
                let backgroundColors;
                if (type === 'custo') {
                    // Tons de vermelho, laranja, roxo para gastos
                    backgroundColors = ['#ef4444', '#f97316', '#dc2626', '#b91c1c', '#7f1d1d', '#9333ea', '#c026d3', '#db2777'];
                } else { // type === 'receita'
                    // Tons de verde, azul para receitas
                    backgroundColors = ['#22c55e', '#10b981', '#06b6d4', '#3b82f6', '#14b8a6', '#4ade80', '#2dd4bf', '#a3e635'];
                }

                 return { labels: Object.keys(categories), datasets: [{ data: Object.values(categories), backgroundColor: backgroundColors.slice(0, Object.keys(categories).length) }] };
            }
            function processMonthlyBalance(receitas, custos) {
                const balanceByMonth = {};
                const monthsLabel = ['Jan','Fev','Mar','Abr','Mai','Jun','Jul','Ago','Set','Out','Nov','Dez'];
                const currentYear = new Date().getFullYear(); 
                monthsLabel.forEach((_, index) => { balanceByMonth[index] = { receitas: 0, custos: 0 }; });
                receitas.forEach(r => { const date = new Date(r.recebimento + 'T00:00:00-03:00'); if(date.getFullYear() === currentYear){ const month = date.getMonth(); if(balanceByMonth[month]) balanceByMonth[month].receitas += parseFloat(r.valor); } });
                 custos.forEach(c => { const date = new Date(c.pagamento + 'T00:00:00-03:00'); if(date.getFullYear() === currentYear){ const month = date.getMonth(); if(balanceByMonth[month]) balanceByMonth[month].custos += parseFloat(c.custo); } });
                const receitaData = monthsLabel.map((_, index) => balanceByMonth[index].receitas);
                const custoData = monthsLabel.map((_, index) => balanceByMonth[index].custos);
                return { labels: monthsLabel, datasets: [ { label: 'Receitas', data: receitaData, backgroundColor: 'rgba(74, 222, 128, 0.2)', borderColor: '#4ade80', borderWidth: 2, tension: 0.4 }, { label: 'Custos', data: custoData, backgroundColor: 'rgba(239, 68, 68, 0.2)', borderColor: '#ef4444', borderWidth: 2, tension: 0.4 } ] };
            }


            function updateChart(chartInstance, data, type, canvasId, loaderId, options = {}) {
                const canvas = document.getElementById(canvasId);
                const loader = document.getElementById(loaderId);
                if (!canvas) {
                    console.warn(`Canvas com ID '${canvasId}' não encontrado.`);
                    return chartInstance; 
                }
                if (chartInstance) chartInstance.destroy(); 
                
                const newChartInstance = new Chart(canvas.getContext('2d'), { 
                    type, 
                    data, 
                    options: { responsive: true, maintainAspectRatio: false, ...options } 
                });
                
                if (loader) loader.style.display = 'none'; 
                
                return newChartInstance;
            }

            function openModal(modal) { modal.classList.add('active'); }
            function closeModal(modal) { modal.classList.remove('active'); }

            function openTransactionModal(type, transaction = null) {
                transactionForm.reset();
                transactionTypeInput.value = type;
                transactionIdInput.value = transaction ? transaction.id : '';
                transactionModalTitle.textContent = transaction ? (type === 'receita' ? 'Editar Receita' : 'Editar Custo') : (type === 'receita' ? 'Nova Receita' : 'Novo Custo');
                custoFields.classList.toggle('hidden', type === 'receita');

                populateSelect(transactionCategoriaSelect, type === 'receita' ? categoriasReceita : categoriasCusto);

                if(transaction){
                    document.getElementById('transaction-titulo').value = transaction.titulo;
                    document.getElementById('transaction-valor').value = transaction.valor || transaction.custo;
                    document.getElementById('transaction-data').value = transaction.data || transaction.pagamento || transaction.recebimento;
                    transactionCategoriaSelect.value = transaction.categoria; 
                    document.getElementById('transaction-observacao').value = transaction.observacao || '';
                    document.getElementById('transaction-efetivado').checked = !!transaction.efetivado;
                     if(type === 'custo'){
                        custoTipoSelect.value = transaction.tipo;
                        custoFormaPagamentoSelect.value = transaction.forma_pagamento;
                     }
                } else {
                    document.getElementById('transaction-data').valueAsDate = new Date();
                }
                openModal(transactionModal);
            }
             function openContaModal(conta = null) {
                contaForm.reset();
                contaIdInput.value = conta ? conta.id : '';
                contaModalTitle.textContent = conta ? 'Editar Conta Bancária' : 'Nova Conta Bancária';
                if(conta){
                    document.getElementById('conta-banco').value = conta.banco;
                    document.getElementById('conta-saldo').value = conta.saldo;
                    document.getElementById('conta-pessoa').value = conta.pessoa;
                    document.getElementById('conta-tipo').value = conta.tipo_conta;
                    document.getElementById('conta-observacao').value = conta.observacao || '';
                }
                openModal(contaModal);
             }
            function openCriptoModal(cripto = null) {
                 criptoForm.reset();
                criptoIdInput.value = cripto ? cripto.id : '';
                criptoModalTitle.textContent = cripto ? 'Editar Criptoativo' : 'Novo Criptoativo';
                 if(cripto){
                    document.getElementById('cripto-moeda').value = cripto.moeda;
                    document.getElementById('cripto-saldo').value = cripto.saldo;
                    document.getElementById('cripto-observacao').value = cripto.observacao || '';
                 }
                openModal(criptoModal);
            }

            // --- Event Listeners ---
            addReceitaBtn?.addEventListener('click', () => openTransactionModal('receita'));
            addCustoBtn?.addEventListener('click', () => openTransactionModal('custo'));
            transactionModal?.querySelector('.close-button').addEventListener('click', () => closeModal(transactionModal));
            addContaBtn?.addEventListener('click', () => openContaModal());
            contaModal?.querySelector('.close-button').addEventListener('click', () => closeModal(contaModal));
            addCriptoBtn?.addEventListener('click', () => openCriptoModal());
            criptoModal?.querySelector('.close-button').addEventListener('click', () => closeModal(criptoModal));

            // Botões de Adicionar das Views Receitas/Custo/Carteira (se existirem)
            document.getElementById('add-conta-btn-view')?.addEventListener('click', () => openContaModal());
            document.getElementById('add-cripto-btn-view')?.addEventListener('click', () => openCriptoModal());


            contasLisEl.addEventListener('click', async (e) => {
                 const editBtn = e.target.closest('.edit-conta-btn');
                const deleteBtn = e.target.closest('.delete-conta-btn');
                const id = editBtn?.dataset.id || deleteBtn?.dataset.id;
                if (!id) return;

                if (editBtn) {
                    const response = await fetchAPI(`/conta/${id}`);
                    if (response?.conta) openContaModal(response.conta);
                } else if (deleteBtn) {
                     if (confirm('Tem a certeza que quer eliminar esta conta?')) {
                        const response = await fetchAPI(`/conta/${id}`, { method: 'DELETE' });
                        if (response?.status) loadAllData(); 
                    }
                }
            });

            criptosLisEl.addEventListener('click', async (e) => {
                 const editBtn = e.target.closest('.edit-cripto-btn');
                const deleteBtn = e.target.closest('.delete-cripto-btn');
                const id = editBtn?.dataset.id || deleteBtn?.dataset.id;
                 if (!id) return;

                 if (editBtn) {
                     const response = await fetchAPI(`/cripto/${id}`);
                     if (response?.cripto) openCriptoModal(response.cripto);
                 } else if (deleteBtn) {
                      if (confirm('Tem a certeza que quer eliminar este criptoativo?')) {
                         const response = await fetchAPI(`/cripto/${id}`, { method: 'DELETE' });
                         if (response?.status) loadAllData();
                    }
                 }
            });

            // Listener para Editar/Eliminar Transações na Tabela
            transactionsTableBody.addEventListener('click', async (e) => {
                 const editBtn = e.target.closest('.edit-transaction-btn');
                 const deleteBtn = e.target.closest('.delete-transaction-btn');
                 const id = editBtn?.dataset.id || deleteBtn?.dataset.id;
                 const type = editBtn?.dataset.type || deleteBtn?.dataset.type;
                 if (!id || !type) return;

                 const endpoint = type === 'receita' ? `/receita/${id}` : `/custo/${id}`;

                 if(editBtn) {
                     const response = await fetchAPI(endpoint);
                     const transactionData = response?.receita || response?.custo; 
                     if(transactionData) openTransactionModal(type, transactionData);

                 } else if (deleteBtn) {
                     if (confirm(`Tem a certeza que quer eliminar esta ${type === 'receita' ? 'receita' : 'custo'}?`)) {
                         const response = await fetchAPI(endpoint, { method: 'DELETE' });
                         if (response?.status) loadAllData();
                    }
                 }
            });


            transactionForm?.addEventListener('submit', async (e) => {
                e.preventDefault();
                const type = transactionTypeInput.value;
                const id = transactionIdInput.value;
                const isEditing = !!id;
                const endpoint = type === 'receita' ? (isEditing ? `/receita/${id}` : '/receita') : (isEditing ? `/custo/${id}` : '/custo'); 
                const method = isEditing ? 'PUT' : 'POST';
                
                const data = {
                    titulo: document.getElementById('transaction-titulo').value,
                    categoria: document.getElementById('transaction-categoria').value,
                    observacao: document.getElementById('transaction-observacao').value,
                    efetivado: document.getElementById('transaction-efetivado').checked ? 1 : 0,
                };

                if (type === 'receita') {
                     data.valor = parseFloat(document.getElementById('transaction-valor').value);
                     data.recebimento = document.getElementById('transaction-data').value;
                } else { // Custo
                     data.custo = parseFloat(document.getElementById('transaction-valor').value);
                     data.pagamento = document.getElementById('transaction-data').value;
                     data.tipo = document.getElementById('custo-tipo').value;
                     data.forma_pagamento = document.getElementById('custo-forma-pagamento').value;
                }

                const response = await fetchAPI(endpoint, { method, body: JSON.stringify(data) });
                 if (response?.status || response?.receita || response?.custo) { 
                   closeModal(transactionModal);
                   loadAllData(); 
                }
            });

            contaForm?.addEventListener('submit', async (e) => {
                 e.preventDefault();
                 const id = contaIdInput.value;
                 const isEditing = !!id;
                 const endpoint = isEditing ? `/conta/${id}` : '/conta';
                 const method = isEditing ? 'PUT' : 'POST';

                 const data = {
                    banco: document.getElementById('conta-banco').value,
                    saldo: parseFloat(document.getElementById('conta-saldo').value),
                    pessoa: document.getElementById('conta-pessoa').value,
                    tipo_conta: document.getElementById('conta-tipo').value,
                    observacao: document.getElementById('conta-observacao').value,
                 };

                 const response = await fetchAPI(endpoint, { method, body: JSON.stringify(data) });
                 if (response?.status || response?.conta) {
                     closeModal(contaModal);
                     loadAllData(); 
                 }
            });

             criptoForm?.addEventListener('submit', async (e) => {
                 e.preventDefault();
                 const id = criptoIdInput.value;
                 const isEditing = !!id;
                 const endpoint = isEditing ? `/cripto/${id}` : '/cripto';
                 const method = isEditing ? 'PUT' : 'POST';

                  const data = {
                    moeda: document.getElementById('cripto-moeda').value,
                    saldo: parseFloat(document.getElementById('cripto-saldo').value),
                    observacao: document.getElementById('cripto-observacao').value,
                 };

                 const response = await fetchAPI(endpoint, { method, body: JSON.stringify(data) });
                 if (response?.status || response?.cripto) {
                    closeModal(criptoModal);
                     loadAllData();
                 }
            });
            
             async function loadAllData() {
                const balanceLoader = document.getElementById('balanceChartLoader');
                const expenseLoader = document.getElementById('expenseChartLoader');
                const incomeLoader = document.getElementById('incomeChartLoader');
                
                if (balanceLoader) balanceLoader.style.display = 'flex';
                if (expenseLoader) expenseLoader.style.display = 'flex';
                if (incomeLoader) incomeLoader.style.display = 'flex'; 

                contasLisEl.innerHTML = `<p class="text-sm text-neutral-500 text-center py-4">A carregar...</p>`;
                criptosLisEl.innerHTML = `<p class="text-sm text-neutral-500 text-center py-4">A carregar...</p>`;
                transactionsTableBody.innerHTML = `<tr><td colspan="5" class="text-center py-6 text-neutral-500">A carregar...</td></tr>`;
                
                // Limpar e preencher as tabelas das views detalhadas
                if (receitasTableBody) receitasTableBody.innerHTML = `<tr><td colspan="5" class="text-center py-6 text-neutral-500">A carregar...</td></tr>`;
                if (custosTableBody) custosTableBody.innerHTML = `<tr><td colspan="5" class="text-center py-6 text-neutral-500">A carregar...</td></tr>`;
                if (contasListView) contasListView.innerHTML = `<p class="text-sm text-neutral-500 text-center py-4">A carregar...</p>`;
                if (criptosListView) criptosListView.innerHTML = `<p class="text-sm text-neutral-500 text-center py-4">A carregar...</p>`;


                 const [receitasRes, custosRes, contasRes, criptosRes] = await Promise.all([
                    fetchAPI('/receita'), fetchAPI('/custo'),
                    fetchAPI('/conta'), fetchAPI('/cripto')
                 ]);
                 
                 const receitas = receitasRes?.data || [];
                 const custos = custosRes?.data || [];
                 const contas = contasRes?.data || [];
                 const criptos = criptosRes?.data || [];

                 renderContas(contas, contasLisEl); // Dashboard Contas
                 renderCriptos(criptos, criptosLisEl); // Dashboard Criptos
                 renderTransacoes(receitas, custos, transactionsTableBody, 10); // Dashboard Transações Recentes
                 calculateTotalsAndCharts(receitas, custos, contas);

                 // Preencher as views detalhadas
                 renderTransacoes(receitas, [], receitasTableBody, Infinity); // Todas as Receitas
                 renderTransacoes([], custos, custosTableBody, Infinity); // Todos os Custos
                 renderContas(contas, contasListView); // Carteira Contas
                 renderCriptos(criptos, criptosListView); // Carteira Criptos
             }
             
              // --- Navegação Topo ---
             navLinks.forEach(link => {
                link.addEventListener('click', (e) => {
                    e.preventDefault();
                    const viewId = link.dataset.view;
                    
                    viewContents.forEach(view => view.classList.add('hidden'));
                    const targetView = document.getElementById(`view-${viewId}`);
                    if(targetView) targetView.classList.remove('hidden');

                    navLinks.forEach(l => l.classList.remove('active'));
                    link.classList.add('active');
                });
            });


             // --- INICIALIZAÇÃO ---
             populateSelect(custoTipoSelect, tiposCusto);
             populateSelect(custoFormaPagamentoSelect, formasPagamento);
             loadAllData();

        });
    </script>
@endpush

