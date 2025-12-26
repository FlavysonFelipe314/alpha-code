<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Plano;
use App\Models\Assinatura;
use App\Models\User;
use App\Http\Services\AsaasService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;

class PagamentoController extends Controller
{
    protected $asaasService;

    public function __construct(AsaasService $asaasService)
    {
        $this->asaasService = $asaasService;
    }

    /**
     * Checkout - Inicia processo de pagamento
     */
    public function checkout(Request $request, $planoId)
    {
        $plano = Plano::where('ativo', true)->findOrFail($planoId);
        
        // Se usuário não está autenticado, redireciona para login
        if (!Auth::check()) {
            return redirect()->route('login')->with('plano_id', $planoId);
        }

        $user = Auth::user();
        return view('pagamento.checkout', compact('plano', 'user'));
    }

    /**
     * Gerar checkout do Asaas
     */
    public function gerarCheckout(Request $request, $planoId)
    {
        $plano = Plano::where('ativo', true)->findOrFail($planoId);
        $user = Auth::user();

        try {
            // Arquitetura correta:
            // - Cartão recorrente → /subscriptions (precisa de cliente e cartão)
            // - Cartão pagamento único → /paymentLinks
            // - PIX → /paymentLinks
            // Como queremos checkout do Asaas, vamos usar /paymentLinks (pagamento único)
            // A assinatura será criada via webhook após confirmação do pagamento
            
            // Criar cliente no Asaas primeiro (se não existir)
            $asaasCustomer = $this->asaasService->criarCliente([
                'name' => $user->name,
                'email' => $user->email,
            ]);

            if (!$asaasCustomer) {
                return back()->withErrors(['error' => 'Erro ao criar cliente. Tente novamente.']);
            }

            // URL de callback após pagamento (URL completa com HTTPS)
            // Asaas EXIGE HTTPS para callbacks
            $callbackUrl = secure_url(route('pagamento.sucesso', ['plano' => $planoId], false));

            // Criar Payment Link para checkout
            $checkoutData = [
                'name' => "Assinatura {$plano->nome}",
                'description' => $plano->descricao ?? "Assinatura do plano {$plano->nome}",
                'value' => (float) $plano->preco,
                'billing_type' => 'CREDIT_CARD', // Apenas cartão para assinaturas
                'external_reference' => "user_{$user->id}_plano_{$plano->id}_recorrente_{$plano->periodicidade}",
                'due_date_limit_days' => 30,
                'callback_url' => $callbackUrl, // URL de retorno após pagamento
            ];

            $checkout = $this->asaasService->criarCheckout($checkoutData);

            if (!$checkout || !isset($checkout['url'])) {
                return back()->withErrors(['error' => 'Erro ao gerar link de pagamento. Tente novamente.']);
            }

            // Criar assinatura pendente no banco
            $assinatura = Assinatura::create([
                'user_id' => $user->id,
                'plano_id' => $plano->id,
                'asaas_customer_id' => $asaasCustomer['id'],
                'asaas_subscription_id' => $checkout['id'], // Payment Link ID
                'status' => 'pending',
                'valor' => $plano->preco,
                'dados_pagamento' => [
                    'payment_link_id' => $checkout['id'],
                    'payment_link_url' => $checkout['url'],
                ],
            ]);

            // Redirecionar para o checkout do Asaas
            return redirect($checkout['url']);
        } catch (\Exception $e) {
            Log::error('Erro ao gerar checkout', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            return back()->withErrors(['error' => 'Erro ao gerar link de pagamento. Tente novamente.']);
        }
    }

    /**
     * Página de sucesso após pagamento
     * Chamada pelo callback do Asaas após pagamento
     */
    public function sucesso(Request $request, $planoId = null)
    {
        $paymentLinkId = $request->get('paymentLink') ?? $request->get('id');
        $planoId = $planoId ?? session('plano_id');
        $user = Auth::user();

        if (!$user) {
            return redirect()->route('login')->with('error', 'Faça login para verificar seu pagamento.');
        }

        if (!$planoId) {
            return redirect()->route('welcome')->with('error', 'Informações de pagamento não encontradas.');
        }

        $plano = Plano::find($planoId);

        if (!$plano) {
            return redirect()->route('welcome')->with('error', 'Plano não encontrado.');
        }

        // Buscar assinatura mais recente do usuário para este plano
        $assinatura = Assinatura::where('user_id', $user->id)
            ->where('plano_id', $planoId)
            ->latest()
            ->first();

        if (!$assinatura) {
            return redirect()->route('welcome')->with('error', 'Assinatura não encontrada. Aguarde alguns instantes ou entre em contato com o suporte.');
        }

        return view('pagamento.sucesso', compact('plano', 'assinatura'));
    }

    /**
     * Webhook do Asaas
     */
    public function webhook(Request $request)
    {
        $event = $request->input('event');
        $payment = $request->input('payment');

        Log::info('Webhook Asaas recebido', [
            'event' => $event,
            'payment' => $payment,
        ]);

        try {
            // Tratar diferentes tipos de eventos do Asaas
            if ($event === 'PAYMENT_CONFIRMED' || $event === 'PAYMENT_RECEIVED') {
                // Buscar assinatura por external_reference
                $externalRef = $payment['externalReference'] ?? null;
                $paymentLinkId = $payment['paymentLink'] ?? null;
                
                $assinatura = null;
                
                // Buscar por external_reference
                if ($externalRef && preg_match('/user_(\d+)_plano_(\d+)_recorrente_(\w+)/', $externalRef, $matches)) {
                    $userId = $matches[1];
                    $planoId = $matches[2];
                    $periodicidade = $matches[3];
                    
                    $assinatura = Assinatura::where('user_id', $userId)
                        ->where('plano_id', $planoId)
                        ->latest()
                        ->first();
                }
                
                // Se não encontrou por external_reference, buscar por payment link ID
                if (!$assinatura && $paymentLinkId) {
                    $assinatura = Assinatura::where('asaas_subscription_id', $paymentLinkId)->first();
                }
                
                if ($assinatura) {
                    // Atualizar assinatura para ativa
                    $fimAssinatura = $assinatura->plano->periodicidade === 'monthly' 
                        ? now()->addMonth() 
                        : now()->addYear();
                    
                    $assinatura->update([
                        'status' => 'active',
                        'inicio' => now(),
                        'fim' => $fimAssinatura,
                        'proximo_pagamento' => isset($payment['dueDate']) 
                            ? date('Y-m-d H:i:s', strtotime($payment['dueDate'])) 
                            : now()->addMonth(),
                    ]);

                    // Atualizar plano do usuário
                    if ($assinatura->user) {
                        $assinatura->user->update(['plano_id' => $assinatura->plano_id]);
                        
                        // Enviar credenciais se for o primeiro pagamento
                        if (!$assinatura->user->password) {
                            $this->enviarCredenciais($assinatura->user);
                        }
                    }
                }
            } elseif ($event === 'PAYMENT_OVERDUE' || $event === 'PAYMENT_REFUSED') {
                $externalRef = $payment['externalReference'] ?? null;
                if ($externalRef && preg_match('/user_(\d+)_plano_(\d+)/', $externalRef, $matches)) {
                    $userId = $matches[1];
                    $planoId = $matches[2];
                    $assinatura = Assinatura::where('user_id', $userId)
                        ->where('plano_id', $planoId)
                        ->latest()
                        ->first();
                    if ($assinatura) {
                        $assinatura->update(['status' => 'expired']);
                    }
                }
            }

            return response()->json(['success' => true], 200);
        } catch (\Exception $e) {
            Log::error('Erro ao processar webhook', [
                'error' => $e->getMessage(),
            ]);
            return response()->json(['success' => false], 500);
        }
    }

    /**
     * Enviar credenciais por email
     */
    private function enviarCredenciais(User $user)
    {
        try {
            // Se o usuário já tem senha, não precisa enviar nova senha
            if ($user->password) {
                // Enviar email de boas-vindas sem senha
                Mail::to($user->email)->send(new \App\Mail\CredenciaisMail($user, null, route('login')));
            } else {
                // Gerar senha temporária para novos usuários
                $senha = Str::random(12);
                $user->update(['password' => Hash::make($senha)]);
                Mail::to($user->email)->send(new \App\Mail\CredenciaisMail($user, $senha, route('login')));
            }
        } catch (\Exception $e) {
            Log::error('Erro ao enviar email de credenciais', [
                'error' => $e->getMessage(),
            ]);
        }
    }
}