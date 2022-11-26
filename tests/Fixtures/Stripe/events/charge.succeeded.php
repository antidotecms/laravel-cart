<?php

return [
    "id" => "evt_3LpOWMJ2WiujycKt1VjHef4B",
    "object" => "event",
    "api_version" => "2022-08-01",
    "created" => 1664941443,
    "data" => [
        "object" => [
            "id" => "ch_3LpOWMJ2WiujycKt1PhQhBhS",
            "object" => "charge",
            "amount" => 419,
            "amount_captured" => 419,
            "amount_refunded" => 0,
            "application" => null,
            "application_fee" => null,
            "application_fee_amount" => null,
            "balance_transaction" => "txn_3LpOWMJ2WiujycKt1ZFCZw32",
            "billing_details" => [
                "address" => [
                    "city" => null,
                    "country" => null,
                    "line1" => null,
                    "line2" => null,
                    "postal_code" => "42424",
                    "state" => null
                ],
                "email" => null,
                "name" => null,
                "phone" => null
            ],
            "calculated_statement_descriptor" => "Stripe",
            "captured" => true,
            "created" => 1664941442,
            "currency" => "gbp",
            "customer" => null,
            "description" => "Order #6",
            "destination" => null,
            "dispute" => null,
            "disputed" => false,
            "failure_balance_transaction" => null,
            "failure_code" => null,
            "failure_message" => null,
            "fraud_details" => [
            ],
            "invoice" => null,
            "livemode" => false,
            "metadata" => [],
            "on_behalf_of" => null,
            "order" => null,
            "outcome" => [
                "network_status" => "approved_by_network",
                "reason" => null,
                "risk_level" => "normal",
                "risk_score" => 35,
                "seller_message" => "Payment complete.",
                "type" => "authorized"
            ],
            "paid" => true,
            "payment_intent" => "pi_3LpOWMJ2WiujycKt1hw9Jhun",
            "payment_method" => "pm_1LpOWbJ2WiujycKt8IDmORg6",
            "payment_method_details" => [
                "card" => [
                    "brand" => "visa",
                    "checks" => [
                        "address_line1_check" => null,
                        "address_postal_code_check" => "pass",
                        "cvc_check" => "pass"
                    ],
                    "country" => "US",
                    "exp_month" => 4,
                    "exp_year" => 2024,
                    "fingerprint" => "ZoVoP0xgtdT0z5ep",
                    "funding" => "credit",
                    "installments" => null,
                    "last4" => "4242",
                    "mandate" => null,
                    "network" => "visa",
                    "three_d_secure" => null,
                    "wallet" => null
                ],
                "type" => "card"
            ],
            "receipt_email" => "tcsmith1978@gmail.com",
            "receipt_number" => null,
            "receipt_url" => "https =>//pay.stripe.com/receipts/payment/CAcaFwoVYWNjdF8xTGtxNWlKMldpdWp5Y0t0KIP785kGMgaU0N2diLY6LBZpdRkiFq8ocRFRYLqhxrnp-KBxIVplXhNKVo-0jrmCOKf774W96ScNqEFQ",
            "refunded" => false,
            "refunds" => [
                "object" => "list",
                "data" => [
                ],
                "has_more" => false,
                "total_count" => 0,
                "url" => "/v1/charges/ch_3LpOWMJ2WiujycKt1PhQhBhS/refunds"
            ],
            "review" => null,
            "shipping" => null,
            "source" => null,
            "source_transfer" => null,
            "statement_descriptor" => null,
            "statement_descriptor_suffix" => null,
            "status" => "succeeded",
            "transfer_data" => null,
            "transfer_group" => null
        ]
    ],
    "livemode" => false,
    "pending_webhooks" => 2,
    "request" => [
        "id" => "req_GhbFwBlRZpF5jA",
        "idempotency_key" => "d9e40ac1-8d1a-490f-a5cd-1bff53a61113"
    ],
    "type" => "charge.succeeded"
];
