<?php

namespace Antidote\LaravelCart\Contracts;

use Antidote\LaravelCart\Concerns\ConfiguresAdjustment;
use Illuminate\Database\Eloquent\Model;

class Adjustment extends Model
{
    use ConfiguresAdjustment;
}
