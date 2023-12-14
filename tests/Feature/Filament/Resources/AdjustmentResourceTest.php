<?php

beforeEach(function() {

    $this->adjustment = \Antidote\LaravelCart\Models\Adjustment::factory()->create([
        'name' => '10% for all orders',
        'class' => \Antidote\LaravelCart\Tests\Fixtures\App\Models\Adjustments\DiscountAdjustmentCalculation::class,
        'parameters' => [
            'type' => 'percentage', //or fixed
            'rate' => 10
        ],
        'is_active' => true
    ]);


    \Antidote\LaravelCart\Models\Adjustment::factory(9)->create();

    $this->not_active_adjustment = \Antidote\LaravelCart\Models\Adjustment::all()->skip(1)->first();
    $this->not_active_adjustment->is_active = false;
    $this->not_active_adjustment->save();

    $this->user = \Antidote\LaravelCart\Tests\Fixtures\App\Models\TestUser::create([
        'name' => 'Test User',
        'email' => 'test@user.com',
        'password' => \Illuminate\Support\Facades\Hash::make('password')
    ]);
});

it('displays the correct form', function($mode, $form) {

    $config = app('config');
    $config->set('laravel-cart.adjustments', [
        'an_adjustment_class' => 'An Adjustment Class',
        'a_second_adjustment_class' => 'A Second Adjustment Class'
    ]);

    $form_config = [
        $form
    ];

    if($mode == 'edit') {

        $form_config[] = [
            'record' => $this->adjustment->id
        ];
    }

    $this->be(\Antidote\LaravelCart\Tests\Fixtures\App\Models\TestUser::factory()->create());

    \Pest\Livewire\livewire(...$form_config)
        ->assertFormFieldExists('name', function(\Filament\Forms\Components\TextInput $field) {
            return $field->isRequired();
        })
        ->assertFormFieldExists('class', function(\Filament\Forms\Components\Select $field) {
            return $field->getOptions() == [
                    'an_adjustment_class' => 'An Adjustment Class',
                    'a_second_adjustment_class' => 'A Second Adjustment Class'
                ] && $field->isRequired()
                && $field->isLive();
        })
        ->assertFormFieldExists('apply_to_subtotal', function(\Filament\Forms\Components\Toggle $field) {
            return $field->getDefaultState() == true;
        })
        ->assertFormFieldExists('is_active', function(\Filament\Forms\Components\Toggle $field) {
            return $field->getDefaultState() == false;
        })
        ->assertSectionLayoutExists('Settings', function(\Filament\Forms\Components\Section $section) use ($mode) {
            return $section->isHidden() == ($mode == 'create');
        })
        ->assertSectionLayoutExists('Settings', function(\Filament\Forms\Components\Section $section) use ($mode) {
            return $section->getChildComponents() == match($mode) {
                'create' => [],
                'edit' => [
                    \Filament\Forms\Components\TextInput::make('test_field')
                ]
            };
        });
})
->with([
    ['create', \Antidote\LaravelCartFilament\Resources\AdjustmentResource\Pages\CreateAdjustment::class],
    ['edit', \Antidote\LaravelCartFilament\Resources\AdjustmentResource\Pages\EditAdjustment::class]
])
->covers(\Antidote\LaravelCartFilament\Resources\AdjustmentResource::class)
->group('adjustment_resource', 'adjustment_resource_form_all');

it('will render the relevant pages', function () {

    $this->withoutExceptionHandling();

    $this->actingAs($this->user)->get(\Antidote\LaravelCartFilament\Resources\AdjustmentResource::getUrl('index'))
        ->assertSuccessful();

    $this->actingAs($this->user)->get(\Antidote\LaravelCartFilament\Resources\AdjustmentResource::getUrl('create'))
        ->assertSuccessful();

    $this->actingAs($this->user)->get(\Antidote\LaravelCartFilament\Resources\AdjustmentResource::getUrl('edit', [
        'record' => $this->adjustment
    ]))->assertSuccessful();

})
->covers(\Antidote\LaravelCartFilament\Resources\AdjustmentResource::class, \Antidote\LaravelCartFilament\Resources\AdjustmentResource\Pages\ListAdjustments::class)
->group('adjustment_resource', 'adjustment_resource_urls');

it('displays the table', function () {

    \Pest\Livewire\livewire(\Antidote\LaravelCartFilament\Resources\AdjustmentResource\Pages\ListAdjustments::class)
        ->assertCanSeeTableRecords(\Antidote\LaravelCart\Models\Adjustment::all())
        ->assertTableColumnStateSet('id', $this->adjustment->id, $this->adjustment)
        ->assertTableColumnStateSet('name', $this->adjustment->name, $this->adjustment)
        ->assertTableColumnStateSet('class', $this->adjustment->class, $this->adjustment)
        ->assertTableColumnExists('is_active', function(\Filament\Tables\Columns\IconColumn $column) {
            return $column->getIcon($column->getState()) == 'heroicon-o-check-badge';
        }, $this->adjustment)
        ->assertTableColumnExists('is_active', function(\Filament\Tables\Columns\IconColumn $column) {
            return $column->getIcon($column->getState()) == 'heroicon-o-x-circle';
        }, $this->not_active_adjustment);
})
->group('adjustment_resource', 'adjustment_resource_table');

it('will create a record', function () {

    \Antidote\LaravelCart\Models\Adjustment::truncate();

    expect(\Antidote\LaravelCart\Models\Adjustment::count())->toBe(0);

    $new_adjustment = \Antidote\LaravelCart\Models\Adjustment::factory()->make([
        'name' => '10% for all orders',
        'class' => \Antidote\LaravelCart\Tests\Fixtures\App\Models\Adjustments\DiscountAdjustmentCalculation::class,
        'parameters' => [
            'type' => 'percentage', //or fixed
            'rate' => 10
        ],
        'is_active' => true
    ]);

    \Pest\Livewire\livewire(\Antidote\LaravelCartFilament\Resources\AdjustmentResource\Pages\CreateAdjustment::class)
        ->fillForm(array_merge($new_adjustment->attributesToArray(), ['parameters' => [
            'type' => 'percentage',
            'rate' => 10,
            'test_field' => 'some test data for the adjustment'
        ]]))
        ->call('create')
        ->assertHasNoFormErrors();

    expect(\Antidote\LaravelCart\Models\Adjustment::count())->toBe(1);

    $saved_adjustment = \Antidote\LaravelCart\Models\Adjustment::first();

    //expect($saved_adjustment->attributesToArray())->toEqual($new_adjustment->attributesToArray());
    expect($saved_adjustment->name)->toEqual($new_adjustment->name);
    expect($saved_adjustment->parameters)->toEqualCanonicalizing([
        'type' => 'percentage',
        'rate' => 10,
        'test_field' => 'some test data for the adjustment'
    ]);
    expect($saved_adjustment->class)->toBe(\Antidote\LaravelCart\Tests\Fixtures\App\Models\Adjustments\DiscountAdjustmentCalculation::class);
    expect($saved_adjustment->is_active)->toBeTruthy();
})
->covers(\Antidote\LaravelCartFilament\Resources\AdjustmentResource\Pages\CreateAdjustment::class);

it('will update a record', function () {

    \Antidote\LaravelCart\Models\Adjustment::truncate();

    expect(\Antidote\LaravelCart\Models\Adjustment::count())->toBe(0);

    $saved_adjustment = \Antidote\LaravelCart\Models\Adjustment::factory()->create([
        'name' => '10% for all orders',
        'class' => \Antidote\LaravelCart\Tests\Fixtures\App\Models\Adjustments\DiscountAdjustmentCalculation::class,
        'parameters' => [
            'type' => 'percentage', //or fixed
            'rate' => 10
        ],
        'is_active' => true
    ]);

    expect(\Antidote\LaravelCart\Models\Adjustment::count())->toBe(1);

    $new_adjustment = \Antidote\LaravelCart\Models\Adjustment::factory()->make([
        'name' => '20% for all orders',
        'class' => \Antidote\LaravelCart\Tests\Fixtures\App\Models\Adjustments\SimpleAdjustmentCalculation::class,
        'is_active' => false
    ]);

    \Pest\Livewire\livewire(\Antidote\LaravelCartFilament\Resources\AdjustmentResource\Pages\EditAdjustment::class ,[
        'record' => $saved_adjustment->id
    ])
        ->fillForm($new_adjustment->attributesToArray())
        ->call('save')
        ->assertHasNoFormErrors();

    expect(\Antidote\LaravelCart\Models\Adjustment::count())->toBe(1);

    $saved_adjustment = \Antidote\LaravelCart\Models\Adjustment::first();

    //expect($saved_adjustment->attributesToArray())->toEqual($new_adjustment->attributesToArray());
    expect($saved_adjustment->name)->toEqual($new_adjustment->name);
    expect($saved_adjustment->parameters)->toEqualCanonicalizing([]);
    expect($saved_adjustment->class)->toBe(\Antidote\LaravelCart\Tests\Fixtures\App\Models\Adjustments\SimpleAdjustmentCalculation::class);
    expect($saved_adjustment->is_active)->toBeFalsy();
})
->covers(\Antidote\LaravelCartFilament\Resources\AdjustmentResource\Pages\EditAdjustment::class);

it('will parameters to an empty array if not supplied', function () {

    $new_adjustment = \Antidote\LaravelCart\Models\Adjustment::factory()->make([
        'name' => '20% for all orders',
        'class' => \Antidote\LaravelCart\Tests\Fixtures\App\Models\Adjustments\SimpleAdjustmentCalculation::class,
        'is_active' => false
    ]);

    $new_adjustment->parameters = null;

    \Pest\Livewire\livewire(\Antidote\LaravelCartFilament\Resources\AdjustmentResource\Pages\CreateAdjustment::class)
        ->fillForm($new_adjustment->attributesToArray())
        ->call('create')
        ->assertHasNoFormErrors();
})
->covers(\Antidote\LaravelCartFilament\Resources\AdjustmentResource\Pages\CreateAdjustment::class);
