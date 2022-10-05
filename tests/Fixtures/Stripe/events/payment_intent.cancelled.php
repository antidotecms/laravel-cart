<?php

return [
    "api_version" => "2022-08-01",
    "created" => 1664909211,
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
            "canceled_at" => 1664909211,
            "cancellation_reason" => "requested_by_customer",
            "capture_method" => "automatic",
            "charges" => [
                "data" => [],
                "has_more" => false,
                "object" => "list",
                "total_count" => 0,
                "url" => "/v1/charges?payment_intent=pi_3LpG8lJ2WiujycKt1GoA5oZ3"
            ],
            "client_secret" => "pi_3LpG8lJ2WiujycKt1GoA5oZ3_secret_QxF0MJhSxvU5YJZd5IxofAkw4",
            "confirmation_method" => "automatic",
            "created" => 1664909211,
            "currency" => "usd",
            "customer" => null,
            "description" => "(created by Stripe CLI)",
            "id" => "pi_3LpG8lJ2WiujycKt1GoA5oZ3",
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
            "status" => "canceled",
            "transfer_data" => null,
            "transfer_group" => null
        ]
    ],
    "id" => "evt_3LpG8lJ2WiujycKt1s0fz7pJ",
    "livemode" => false,
    "object" => "event",
    "pending_webhooks" => 2,
    "request" => [
        "id" => "req_1gozKIMcMAnUCo",
        "idempotency_key" => "03096b41-56ae-45de-a8c4-4623157cf5ad"
    ],
    "type" => "payment_intent.canceled"
];
