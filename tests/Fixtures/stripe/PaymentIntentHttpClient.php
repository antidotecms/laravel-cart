<?php

namespace Antidote\LaravelCart\Tests\Fixtures\stripe;

use Stripe\ApiRequestor;
use Stripe\HttpClient\ClientInterface;

class PaymentIntentHttpClient implements ClientInterface
{
    private array $params = [];
    private int $http_code = 200;
    private array $headers = [];
    private string $exception_class = '';

    public function __construct()
    {
        ApiRequestor::setHttpClient($this);
    }

    public function with($key, $value): self
    {
        $this->params[$key] = $value;

        return $this;
    }

    public function withHttpCode($http_code): self
    {
        $this->http_code = $http_code;

        return $this;
    }

    public function throwException($exception_class)
    {
        $this->exception_class = $exception_class;
    }

    public function withHeader($key, $value): self
    {
        $this->headers[$key] = $value;

        return $this;
    }

    public function request($method, $absUrl, $headers, $params, $hasFile)
    {
        if($this->exception_class) {
            throw new $this->exception_class;
        }

        return [
            $this->response(),
            $this->http_code,
            $this->headers
        ];
    }

    private function response(): string
    {
        if($this->exception_class) {
            throw new $this->exception_class;
        }

        return json_encode(array_merge([
            'id' => 'xxx',
            'object' => 'payment_intent',
            'amount' => 2000,
            'amount_capturable' => 0,
            'amount_details' => [
                'tip' => [
                ],
            ],
            'amount_received' => 0,
            'application' => NULL,
            'application_fee_amount' => NULL,
            'automatic_payment_methods' => NULL,
            'canceled_at' => NULL,
            'cancellation_reason' => NULL,
            'capture_method' => 'automatic',
            'charges' => [
                'object' => 'list',
                'data' => [
                ],
                'has_more' => false,
                'url' => '/v1/charges?payment_intent=pi_1JKS5I2x6R10KRrhk9GzY4BM',
            ],
            'client_secret' => 'xxx',
            'confirmation_method' => 'automatic',
            'created' => 1628014284,
            'currency' => 'usd',
            'customer' => NULL,
            'description' => 'Created by stripe.com/docs demo',
            'invoice' => NULL,
            'last_payment_error' => NULL,
            'livemode' => false,
            'metadata' => [
            ],
            'next_action' => NULL,
            'on_behalf_of' => NULL,
            'payment_method' => NULL,
            'payment_method_options' => [
                'card' => [
                    'installments' => NULL,
                    'mandate_options' => NULL,
                    'network' => NULL,
                    'request_three_d_secure' => 'automatic',
                ],
            ],
            'payment_method_types' => [
                0 => 'card',
            ],
            'processing' => NULL,
            'receipt_email' => NULL,
            'review' => NULL,
            'setup_future_usage' => NULL,
            'shipping' => NULL,
            'statement_descriptor' => NULL,
            'statement_descriptor_suffix' => NULL,
            'status' => 'requires_payment_method',
            'transfer_data' => NULL,
            'transfer_group' => NULL,
        ], $this->params));
    }
}
