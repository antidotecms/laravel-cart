<?php

namespace Antidote\LaravelCart\Tests;

class StripeTestCase extends TestCase
{
    protected function migrateUsing()
    {
        return [
            '--path' => [
                './database/migrations/stripe'
            ]
        ];
    }
}
