<?php

namespace App\Http\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class AsaasService
{
    private $apiKey;
    private $baseUrl;
    private $environment;

    public function __construct()
    {
        $this->apiKey = config('services.asaas.api_key');
        $this->environment = config('services.asaas.environment', 'sandbox');
        $this->baseUrl = $this->environment === 'production' 
            ? 'https://www.asaas.com/api/v3'
            : 'https://sandbox.asaas.com/api/v3';
    }

    /**
     * Criar cliente no Asaas
     */
    public function criarCliente($data)
    {
        try {
            $response = Http::withHeaders([
                'access_token' => $this->apiKey,
                'Content-Type' => 'application/json',
            ])->post("{$this->baseUrl}/customers", [
                'name' => $data['name'],
                'email' => $data['email'],
                'cpfCnpj' => $data['cpfCnpj'] ?? null,
                'phone' => $data['phone'] ?? null,
                'mobilePhone' => $data['mobilePhone'] ?? null,
                'postalCode' => $data['postalCode'] ?? null,
                'address' => $data['address'] ?? null,
                'addressNumber' => $data['addressNumber'] ?? null,
                'complement' => $data['complement'] ?? null,
                'province' => $data['province'] ?? null,
                'city' => $data['city'] ?? null,
                'state' => $data['state'] ?? null,
            ]);

            if ($response->successful()) {
                return $response->json();
            }

            // Se o cliente já existe (email duplicado), buscar pelo email
            if ($response->status() === 400) {
                $error = $response->json();
                if (isset($error['errors']) && is_array($error['errors'])) {
                    foreach ($error['errors'] as $err) {
                        if (isset($err['code']) && $err['code'] === 'invalid_customer') {
                            // Buscar cliente existente por email
                            $existingCustomer = Http::withHeaders([
                                'access_token' => $this->apiKey,
                            ])->get("{$this->baseUrl}/customers", [
                                'email' => $data['email'],
                            ]);

                            if ($existingCustomer->successful()) {
                                $customers = $existingCustomer->json();
                                if (isset($customers['data']) && count($customers['data']) > 0) {
                                    return $customers['data'][0]; // Retornar cliente existente
                                }
                            }
                        }
                    }
                }
            }

            Log::error('Erro ao criar cliente no Asaas', [
                'status' => $response->status(),
                'response' => $response->json(),
            ]);

            return null;
        } catch (\Exception $e) {
            Log::error('Exceção ao criar cliente no Asaas', [
                'message' => $e->getMessage(),
            ]);
            return null;
        }
    }

    /**
     * Criar link de pagamento (checkout) no Asaas
     * IMPORTANTE: Payment Links são APENAS para pagamentos únicos
     * NÃO suporta subscriptionCycle - use criarAssinatura() para assinaturas
     * Conforme documentação: https://docs.asaas.com/docs/criando-um-link-de-pagamentos
     */
    public function criarCheckout($data)
    {
        try {
            // Montar payload para Payment Link (APENAS pagamento único)
            // Endpoint: POST /v3/paymentLinks
            $billingType = strtoupper($data['billing_type'] ?? 'CREDIT_CARD');
            $paymentMethods = $this->convertBillingTypeToPaymentMethods($billingType);
            
            $payload = [
                'name' => $data['name'],
                'description' => $data['description'] ?? $data['name'],
                'value' => (float) $data['value'],
                'paymentMethods' => $paymentMethods, // Obrigatório - UI do checkout
                'chargeType' => 'DETACHED', // Obrigatório para Payment Links
                'dueDateLimitDays' => isset($data['due_date_limit_days']) 
                    ? (int) $data['due_date_limit_days'] 
                    : 30,
            ];
            
            // IMPORTANTE: Quando há CREDIT_CARD, billingType também é obrigatório
            // O Asaas usa paymentMethods para UI e billingType para o motor de cobrança
            if (in_array('CREDIT_CARD', $paymentMethods)) {
                $payload['billingType'] = 'CREDIT_CARD';
            }

            // Campos opcionais
            if (isset($data['due_date']) && !empty($data['due_date'])) {
                $payload['dueDate'] = $data['due_date'];
            }

            if (isset($data['external_reference']) && !empty($data['external_reference'])) {
                $payload['externalReference'] = $data['external_reference'];
            }

            // URL de callback após pagamento (opcional)
            if (isset($data['callback_url']) && !empty($data['callback_url'])) {
                $payload['callback'] = [
                    'successUrl' => $data['callback_url'],
                ];
            }

            // Verificar se a API key está configurada
            if (empty($this->apiKey)) {
                Log::error('ASAAS_API_KEY não configurada no .env');
                return null;
            }

            // URL do endpoint Payment Links - IMPORTANTE: usar /paymentLinks, não /payments
            $url = "{$this->baseUrl}/paymentLinks";
            
            // Log completo para debug
            Log::info('Asaas PaymentLink request', [
                'url' => $url,
                'base_url' => $this->baseUrl,
                'payload' => $payload,
                'has_paymentMethods' => isset($payload['paymentMethods']),
                'has_billingType' => isset($payload['billingType']),
                'has_chargeType' => isset($payload['chargeType']),
                'paymentMethods_value' => $payload['paymentMethods'] ?? 'NOT SET',
                'billingType_value' => $payload['billingType'] ?? 'NOT SET',
                'chargeType_value' => $payload['chargeType'] ?? 'NOT SET',
            ]);

            $response = Http::withHeaders([
                'access_token' => $this->apiKey,
                'Content-Type' => 'application/json',
            ])->post($url, $payload);

            if ($response->successful()) {
                return $response->json();
            }

            Log::error('Erro ao criar checkout no Asaas', [
                'status' => $response->status(),
                'response' => $response->json(),
                'payload_sent' => $payload,
            ]);

            return null;
        } catch (\Exception $e) {
            Log::error('Exceção ao criar checkout no Asaas', [
                'message' => $e->getMessage(),
            ]);
            return null;
        }
    }

    /**
     * Converter billingType para paymentMethods array
     * Payment Links usam paymentMethods, não billingType
     */
    private function convertBillingTypeToPaymentMethods($billingType)
    {
        $billingType = strtoupper($billingType);
        
        // Mapear billingType para paymentMethods array
        switch ($billingType) {
            case 'PIX':
                return ['PIX'];
            case 'CREDIT_CARD':
                return ['CREDIT_CARD'];
            case 'BOLETO':
                return ['BOLETO'];
            default:
                return ['PIX', 'CREDIT_CARD', 'BOLETO']; // Todos os métodos como fallback
        }
    }

    /**
     * Criar assinatura no Asaas
     * Use este método para assinaturas recorrentes
     * NÃO use Payment Links para assinaturas
     */
    public function criarAssinatura($data)
    {
        try {
            $payload = [
                'customer' => $data['customer_id'],
                'billingType' => strtoupper($data['billing_type'] ?? 'CREDIT_CARD'), // Assinaturas só funcionam com CREDIT_CARD ou BOLETO
                'value' => $data['value'],
                'nextDueDate' => $data['next_due_date'],
                'cycle' => strtoupper($data['cycle'] ?? 'MONTHLY'),
                'description' => $data['description'] ?? null,
                'externalReference' => $data['external_reference'] ?? null,
            ];

            // Adicionar token do cartão se fornecido
            if (isset($data['credit_card_token']) && $data['credit_card_token']) {
                $payload['creditCardToken'] = $data['credit_card_token'];
            }

            $response = Http::withHeaders([
                'access_token' => $this->apiKey,
                'Content-Type' => 'application/json',
            ])->post("{$this->baseUrl}/subscriptions", $payload);

            if ($response->successful()) {
                return $response->json();
            }

            Log::error('Erro ao criar assinatura no Asaas', [
                'status' => $response->status(),
                'response' => $response->json(),
            ]);

            return null;
        } catch (\Exception $e) {
            Log::error('Exceção ao criar assinatura no Asaas', [
                'message' => $e->getMessage(),
            ]);
            return null;
        }
    }

    /**
     * Processar webhook do Asaas
     */
    public function processarWebhook($event, $payment)
    {
        // Lógica de processamento de webhook
        return true;
    }

    /**
     * Buscar assinatura no Asaas
     */
    public function buscarAssinatura($subscriptionId)
    {
        try {
            $response = Http::withHeaders([
                'access_token' => $this->apiKey,
            ])->get("{$this->baseUrl}/subscriptions/{$subscriptionId}");

            if ($response->successful()) {
                return $response->json();
            }

            return null;
        } catch (\Exception $e) {
            Log::error('Exceção ao buscar assinatura no Asaas', [
                'message' => $e->getMessage(),
            ]);
            return null;
        }
    }

    /**
     * Cancelar assinatura no Asaas
     */
    public function cancelarAssinatura($subscriptionId)
    {
        try {
            $response = Http::withHeaders([
                'access_token' => $this->apiKey,
            ])->delete("{$this->baseUrl}/subscriptions/{$subscriptionId}");

            return $response->successful();
        } catch (\Exception $e) {
            Log::error('Exceção ao cancelar assinatura no Asaas', [
                'message' => $e->getMessage(),
            ]);
            return false;
        }
    }
}