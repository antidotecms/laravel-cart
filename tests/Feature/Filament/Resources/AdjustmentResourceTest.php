<?php

use Antidote\LaravelCart\Models\Adjustment;
use Antidote\LaravelCart\Tests\Fixtures\App\Models\Adjustments\DiscountAdjustmentCalculation;
use Antidote\LaravelCart\Tests\Fixtures\App\Models\Adjustments\SimpleAdjustmentCalculation;
use Antidote\LaravelCart\Tests\Fixtures\App\Models\TestUser;
use Antidote\LaravelCartFilament\CartPanelPlugin;
use Antidote\LaravelCartFilament\Resources\AdjustmentResource;
use Antidote\LaravelCartFilament\Resources\AdjustmentResource\Pages\CreateAdjustment;
use Antidote\LaravelCartFilament\Resources\AdjustmentResource\Pages\EditAdjustment;
use Antidote\LaravelCartFilament\Resources\AdjustmentResource\Pages\ListAdjustments;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Tables\Columns\IconColumn;
use Illuminate\Support\Facades\Hash;
use function Pest\Livewire\livewire;

beforeEach(function() {

    $this->adjustment = Adjustment::factory()->create([
        'name' => '10% for all orders',
        'class' => DiscountAdjustmentCalculation::class,
        'parameters' => [
            'type' => 'percentage', //or fixed
            'rate' => 10
        ],
        'is_active' => true
    ]);


    Adjustment::factory(9)->create();

    $this->not_active_adjustment = Adjustment::all()->skip(1)->first();
    $this->not_active_adjustment->is_active = false;
    $this->not_active_adjustment->save();

    $this->user = TestUser::create([
        'name' => 'Test User',
        'email' => 'test@user.com',
        'password' => Hash::make('password')
    ]);
});

it('displays the correct form', function($mode, $form) {

    CartPanelPlugin::set('adjustments', [
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

    $this->be(TestUser::factory()->create());

    livewire(...$form_config)
        ->assertFormFieldExists('name', function(TextInput $field) {
            return $field->isRequired();
        })
        ->assertFormFieldExists('class', function(Select $field) {
            return $field->getOptions() == [
                    'an_adjustment_class' => 'An Adjustment Class',
                    'a_second_adjustment_class' => 'A Second Adjustment Class'
                ] && $field->isRequired()
                && $field->isLive();
        })
        ->assertFormFieldExists('apply_to_subtotal', function(Toggle $field) {
            return $field->getDefaultState() == true;
        })
        ->assertFormFieldExists('is_active', function(Toggle $field) {
            return $field->getDefaultState() == false;
        })
        ->assertSectionLayoutExists('Settings', function(Section $section) use ($mode) {
            return $section->isHidden() == ($mode == 'create');
        })
        ->assertSectionLayoutExists('Settings', function(Section $section) use ($mode) {
            return $section->getChildComponents() == match($mode) {
                'create' => [],
                'edit' => [
                    TextInput::make('test_field')
                ]
            };
        });
})
->with([
    ['create', CreateAdjustment::class],
    ['edit', EditAdjustment::class]
])
->covers(AdjustmentResource::class)
->group('adjustment_resource', 'adjustment_resource_form_all');

it('will render the relevant pages', function () {

    $this->withoutExceptionHandling();

    $this->actingAs($this->user)->get(AdjustmentResource::getUrl('index'))
        ->assertSuccessful();

    $this->actingAs($this->user)->get(AdjustmentResource::getUrl('create'))
        ->assertSuccessful();

    $this->actingAs($this->user)->get(AdjustmentResource::getUrl('edit', [
        'record' => $this->adjustment
    ]))->assertSuccessful();

})
->covers(AdjustmentResource::class, ListAdjustments::class)
->group('adjustment_resource', 'adjustment_resource_urls');

it('displays the table', function () {

    livewire(ListAdjustments::class)
        ->assertCanSeeTableRecords(Adjustment::all())
        ->assertTableColumnStateSet('id', $this->adjustment->id, $this->adjustment)
        ->assertTableColumnStateSet('name', $this->adjustment->name, $this->adjustment)
        ->assertTableColumnStateSet('class', $this->adjustment->class, $this->adjustment)
        ->assertTableColumnExists('is_active', function(IconColumn $column) {
            return $column->getIcon($column->getState()) == 'heroicon-o-check-badge';
        }, $this->adjustment)
        ->assertTableColumnExists('is_active', function(IconColumn $column) {
            return $column->getIcon($column->getState()) == 'heroicon-o-x-circle';
        }, $this->not_active_adjustment);
})
->group('adjustment_resource', 'adjustment_resource_table');

it('will create a record', function () {

    Adjustment::truncate();

    expect(Adjustment::count())->toBe(0);

    $new_adjustment = Adjustment::factory()->make([
        'name' => '10% for all orders',
        'class' => DiscountAdjustmentCalculation::class,
        'parameters' => [
            'type' => 'percentage', //or fixed
            'rate' => 10
        ],
        'is_active' => true
    ]);

    livewire(CreateAdjustment::class)
        ->fillForm(array_merge($new_adjustment->attributesToArray(), ['parameters' => [
            'type' => 'percentage',
            'rate' => 10,
            'test_field' => 'some test data for the adjustment'
        ]]))
        ->call('create')
        ->assertHasNoFormErrors();

    expect(Adjustment::count())->toBe(1);

    $saved_adjustment = Adjustment::first();

    //expect($saved_adjustment->attributesToArray())->toEqual($new_adjustment->attributesToArray());
    expect($saved_adjustment->name)->toEqual($new_adjustment->name);
    expect($saved_adjustment->parameters)->toEqualCanonicalizing([
        'type' => 'percentage',
        'rate' => 10,
        'test_field' => 'some test data for the adjustment'
    ]);
    expect($saved_adjustment->class)->toBe(DiscountAdjustmentCalculation::class);
    expect($saved_adjustment->is_active)->toBeTruthy();
})
->covers(CreateAdjustment::class);

it('will update a record', function () {

    Adjustment::truncate();

    expect(Adjustment::count())->toBe(0);

    $saved_adjustment = Adjustment::factory()->create([
        'name' => '10% for all orders',
        'class' => DiscountAdjustmentCalculation::class,
        'parameters' => [
            'type' => 'percentage', //or fixed
            'rate' => 10
        ],
        'is_active' => true
    ]);

    expect(Adjustment::count())->toBe(1);

    $new_adjustment = Adjustment::factory()->make([
        'name' => '20% for all orders',
        'class' => SimpleAdjustmentCalculation::class,
        'is_active' => false
    ]);

    livewire(EditAdjustment::class ,[
        'record' => $saved_adjustment->id
    ])
        ->fillForm($new_adjustment->attributesToArray())
        ->call('save')
        ->assertHasNoFormErrors();

    expect(Adjustment::count())->toBe(1);

    $saved_adjustment = Adjustment::first();

    //expect($saved_adjustment->attributesToArray())->toEqual($new_adjustment->attributesToArray());
    expect($saved_adjustment->name)->toEqual($new_adjustment->name);
    expect($saved_adjustment->parameters)->toEqualCanonicalizing([]);
    expect($saved_adjustment->class)->toBe(SimpleAdjustmentCalculation::class);
    expect($saved_adjustment->is_active)->toBeFalsy();
})
->covers(EditAdjustment::class);

it('will parameters to an empty array if not supplied', function () {

    $new_adjustment = Adjustment::factory()->make([
        'name' => '20% for all orders',
        'class' => SimpleAdjustmentCalculation::class,
        'is_active' => false
    ]);

    $new_adjustment->parameters = null;

    livewire(CreateAdjustment::class)
        ->fillForm($new_adjustment->attributesToArray())
        ->call('create')
        ->assertHasNoFormErrors();
})
->covers(CreateAdjustment::class);
