<?php

use Illuminate\Foundation\Testing\RefreshDatabase;

uses(\Tests\TestCase::class)->in(__DIR__);

uses(RefreshDatabase::class)->in('Feature');

//uses(\Tests\Fixtures\app\Models\Products\Product::class)->in('Feature');
