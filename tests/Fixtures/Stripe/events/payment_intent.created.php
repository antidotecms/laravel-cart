<?php

return [
    "api_version" => "2022-08-01",
    "created" => 1664896275,
    "data" => [
        "object" => [
            "amount" => 2000,
            "amount_capturable" => 0,
            "amount_details" => [
                "tip" => []
            ],
            "amount_received" => 0,
            "application" => null,
            "application_fee_amount" => null,
            "automatic_payment_methods" => null,
            "canceled_at" => null,
            "cancellation_reason" => null,
            "capture_method" => "automatic",
            "charges" => [
                "data" => [],
                "has_more" => false,
                "object" => "list",
                "total_count" => 0,
                "url" => "/v1/charges?payment_intent=pi_3LpCm7J2WiujycKt1MCYkEC6"
            ],
            "client_secret" => "pi_3LpCm7J2WiujycKt1MCYkEC6_secret_vUOb4Hv3zyUxUvg2kEhiRQEa8",
            "confirmation_method" => "automatic",
            "created" => 1664896275,
            "currency" => "usd",
            "customer" => null,
            "description" => "(created by Stripe CLI)",
            "id" => "pi_3LpCm7J2WiujycKt1MCYkEC6",
            "invoice" => null,
            "last_payment_error" => null,
            "livemode" => false,
            "metadata" => [],
            "next_action" => null,
            "object" => "payment_intent",
            "on_behalf_of" => null,
            "payment_method" => null,
            "payment_method_options" => [
                "card" => [
                    "installments" => null,
                    "mandate_options" => null,
                    "network" => null,
                    "request_three_d_secure" => "automatic"
                ]
            ],
            "payment_method_types" => [
                "card"
            ],
            "processing" => null,
            "receipt_email" => null,
            "review" => null,
            "setup_future_usage" => null,
            "shipping" => null,
            "source" => null,
            "statement_descriptor" => null,
            "statement_descriptor_suffix" => null,
            "status" => "requires_payment_method",
            "transfer_data" => null,
            "transfer_group" => null
        ]
    ],
    "id" => "evt_3LpCm7J2WiujycKt1oDrDVO6",
    "livemode" => false,
    "object" => "event",
    "pending_webhooks" => 2,
    "request" => [
        "id" => "req_9Jq2p7qPFqXLQi",
        "idempotency_key" => "3a1fb1e3-06ae-4151-9497-49b47e286aca"
    ],
    "type" => "payment_intent.created"
];
