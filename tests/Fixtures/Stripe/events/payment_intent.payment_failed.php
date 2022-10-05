<?php

return [
    "api_version" => "2022-08-01",
    "created" => 1664908543,
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
                "data" => [
                    [
                        "amount" => 2000,
                        "amount_captured" => 0,
                        "amount_refunded" => 0,
                        "application" => null,
                        "application_fee" => null,
                        "application_fee_amount" => null,
                        "balance_transaction" => null,
                        "billing_details" => [
                            "address" => [
                                "city" => null,
                                "country" => null,
                                "line1" => null,
                                "line2" => null,
                                "postal_code" => null,
                                "state" => null
                            ],
                            "email" => null,
                            "name" => null,
                            "phone" => null
                        ],
                        "calculated_statement_descriptor" => "Stripe",
                        "captured" => false,
                        "created" => 1664908542,
                        "currency" => "usd",
                        "customer" => null,
                        "description" => "(created by Stripe CLI)",
                        "destination" => null,
                        "dispute" => null,
                        "disputed" => false,
                        "failure_balance_transaction" => null,
                        "failure_code" => "card_declined",
                        "failure_message" => "Your card was declined.",
                        "fraud_details" => [],
                        "id" => "ch_3LpFxyJ2WiujycKt0XOr8kkN",
                        "invoice" => null,
                        "livemode" => false,
                        "metadata" => [],
                        "object" => "charge",
                        "on_behalf_of" => null,
                        "order" => null,
                        "outcome" => [
                            "network_status" => "declined_by_network",
                            "reason" => "generic_decline",
                            "risk_level" => "normal",
                            "risk_score" => 1,
                            "seller_message" => "The bank did not return any further details with this decline.",
                            "type" => "issuer_declined"
                        ],
                        "paid" => false,
                        "payment_intent" => "pi_3LpFxyJ2WiujycKt0zs0aZkZ",
                        "payment_method" => "pm_1LpFxyJ2WiujycKteL8QUp6M",
                        "payment_method_details" => [
                            "card" => [
                                "brand" => "visa",
                                "checks" => [
                                    "address_line1_check" => null,
                                    "address_postal_code_check" => null,
                                    "cvc_check" => null
                                ],
                                "country" => "US",
                                "exp_month" => 10,
                                "exp_year" => 2023,
                                "fingerprint" => "iaOg2wcLOvkrCzcM",
                                "funding" => "credit",
                                "installments" => null,
                                "last4" => "0002",
                                "mandate" => null,
                                "network" => "visa",
                                "three_d_secure" => null,
                                "wallet" => null
                            ],
                            "type" => "card"
                        ],
                        "receipt_email" => null,
                        "receipt_number" => null,
                        "receipt_url" => null,
                        "refunded" => false,
                        "refunds" => [
                            "data" => [],
                            "has_more" => false,
                            "object" => "list",
                            "total_count" => 0,
                            "url" => "/v1/charges/ch_3LpFxyJ2WiujycKt0XOr8kkN/refunds"
                        ],
                        "review" => null,
                        "shipping" => null,
                        "source" => null,
                        "source_transfer" => null,
                        "statement_descriptor" => null,
                        "statement_descriptor_suffix" => null,
                        "status" => "failed",
                        "transfer_data" => null,
                        "transfer_group" => null
                    ]
                ],
                "has_more" => false,
                "object" => "list",
                "total_count" => 1,
                "url" => "/v1/charges?payment_intent=pi_3LpFxyJ2WiujycKt0zs0aZkZ"
            ],
            "client_secret" => "pi_3LpFxyJ2WiujycKt0zs0aZkZ_secret_4EKZnwqwWkwyZoqxalIeYj1b0",
            "confirmation_method" => "automatic",
            "created" => 1664908542,
            "currency" => "usd",
            "customer" => null,
            "description" => "(created by Stripe CLI)",
            "id" => "pi_3LpFxyJ2WiujycKt0zs0aZkZ",
            "invoice" => null,
            "last_payment_error" => [
                "charge" => "ch_3LpFxyJ2WiujycKt0XOr8kkN",
                "code" => "card_declined",
                "decline_code" => "generic_decline",
                "doc_url" => "https =>//stripe.com/docs/error-codes/card-declined",
                "message" => "Your card was declined.",
                "payment_method" => [
                    "billing_details" => [
                        "address" => [
                            "city" => null,
                            "country" => null,
                            "line1" => null,
                            "line2" => null,
                            "postal_code" => null,
                            "state" => null
                        ],
                        "email" => null,
                        "name" => null,
                        "phone" => null
                    ],
                    "card" => [
                        "brand" => "visa",
                        "checks" => [
                            "address_line1_check" => null,
                            "address_postal_code_check" => null,
                            "cvc_check" => null
                        ],
                        "country" => "US",
                        "exp_month" => 10,
                        "exp_year" => 2023,
                        "fingerprint" => "iaOg2wcLOvkrCzcM",
                        "funding" => "credit",
                        "generated_from" => null,
                        "last4" => "0002",
                        "networks" => [
                            "available" => [
                                "visa"
                            ],
                            "preferred" => null
                        ],
                        "three_d_secure_usage" => [
                            "supported" => true
                        ],
                        "wallet" => null
                    ],
                    "created" => 1664908542,
                    "customer" => null,
                    "id" => "pm_1LpFxyJ2WiujycKteL8QUp6M",
                    "livemode" => false,
                    "metadata" => [],
                    "object" => "payment_method",
                    "type" => "card"
                ],
                "type" => "card_error"
            ],
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
    "id" => "evt_3LpFxyJ2WiujycKt0Q8LoUGP",
    "livemode" => false,
    "object" => "event",
    "pending_webhooks" => 2,
    "request" => [
        "id" => "req_AHPXl6iJqhTn5m",
        "idempotency_key" => "5d2f5b5a-1a8c-4f28-aa84-45a8e35ee82f"
    ],
    "type" => "payment_intent.payment_failed"
];
