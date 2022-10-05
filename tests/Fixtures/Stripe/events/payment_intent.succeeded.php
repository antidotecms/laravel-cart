<?php

return [
    "api_version" => "2022-08-01",
    "created" => 1664908950,
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
                "url" => "/v1/charges?payment_intent=pi_3LpG4YJ2WiujycKt1Qxq7c2D"
            ],
            "client_secret" => "pi_3LpG4YJ2WiujycKt1Qxq7c2D_secret_Z4xLbiV5XNawHrWEIJ3yyB9mo",
            "confirmation_method" => "automatic",
            "created" => 1664908950,
            "currency" => "usd",
            "customer" => null,
            "description" => "(created by Stripe CLI)",
            "id" => "pi_3LpG4YJ2WiujycKt1Qxq7c2D",
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
            "shipping" => [
                "address" => [
                    "city" => "San Francisco",
                    "country" => "US",
                    "line1" => "510 Townsend St",
                    "line2" => null,
                    "postal_code" => "94103",
                    "state" => "CA"
                ],
                "carrier" => null,
                "name" => "Jenny Rosen",
                "phone" => null,
                "tracking_number" => null
            ],
            "source" => null,
            "statement_descriptor" => null,
            "statement_descriptor_suffix" => null,
            "status" => "requires_payment_method",
            "transfer_data" => null,
            "transfer_group" => null
        ]
    ],
    "id" => "evt_3LpG4YJ2WiujycKt1Oy4B6ru",
    "livemode" => false,
    "object" => "event",
    "pending_webhooks" => 2,
    "request" => [
        "id" => "req_IRER50uWvgR94r",
        "idempotency_key" => "c3244bf2-991d-460b-9140-342e3eba7f8e"
    ],
    "type" => "payment_intent.created"

];
