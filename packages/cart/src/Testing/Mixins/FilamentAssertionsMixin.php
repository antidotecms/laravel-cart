<?php

namespace Antidote\LaravelCart\Testing\Mixins;

use Filament\Forms\ComponentContainer;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Section;
use Filament\Resources\Pages\Page;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables\Actions\Action;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Testing\Assert;
use Livewire\Testing\TestableLivewire;

/**
 * @mixin TestableLivewire
 */
class FilamentAssertionsMixin
{
    public function assertTableActionHasModalContentViewName()
    {
        return function(string $name, string $view, $record = null) {
            $this->assertTableActionExists($name);

            if (! $record instanceof Model) {
                $record = $this->instance()->getTableRecord($record);
            }

            $livewire = $this->instance();

            /** @var Action $action */
            $action = $livewire->getTable()->getAction($name) ?? $livewire->getCachedTableEmptyStateAction($name) ?? $livewire->getCachedTableHeaderAction($name);

            $action->record($record);

            $modalView = $action->getModalContent();

            if($modalView instanceof \Illuminate\View\View) {
                Assert::assertEquals(
                    $view,
                    $modalView->name(),
                    "The modal content does not return a view named {$name}"
                );
            }

            return $this;
        };
    }

    public function assertTableActionHasModalContentViewData()
    {
        return function(string $name, array $data, $record = null) {
            $this->assertTableActionExists($name);

            if (! $record instanceof Model) {
                $record = $this->instance()->getTableRecord($record);
            }

            $livewire = $this->instance();

            /** @var Action $action */
            $action = $livewire->getTable()->getAction($name) ?? $livewire->getCachedTableEmptyStateAction($name) ?? $livewire->getCachedTableHeaderAction($name);

            $action->record($record);

            $modalView = $action->getModalContent();

            $dataOutput = print_r($data, true);
            if($modalView instanceof \Illuminate\View\View) {
                Assert::assertEquals(
                    $data,
                    $modalView->getData(),
                    "The modal content does not contain the data {$dataOutput}"
                );
            }

            return $this;
        };
    }

    public function assertSectionLayoutExists() {
        return function(string $heading, string | \Closure $formName = 'form', ?\Closure $callback = null): static {

            if ($formName instanceof \Closure) {
                $callback = $formName;
                $formName = 'form';
            }

            /** @phpstan-ignore-next-line  */
            $this->assertFormExists($formName);

            $livewire = $this->instance();
            $livewireClass = $livewire::class;

            /** @var ComponentContainer $form */
            $form = $livewire->{$formName};

            $layouts = collect($form->getFlatComponents(withHidden: true))->reject(function ($component) {
                return !is_a($component, Section::class);
            });

            //remove main container
            //$layouts->shift();

            $layout = $layouts->filter(function($item) use ($heading) {
                return $item->getHeading() == $heading;
            })->first();

            Assert::assertInstanceOf(
                \Filament\Support\Components\Component::class,
                $layout,
                "Failed asserting that a layout component with the name [{$heading}] exists on the form with the name [{$formName}] on the [{$livewireClass}] component."
            );

            if ($callback) {
                Assert::assertTrue(
                    $callback($layout),
                    "Failed asserting that a layout component with the name [{$heading}] and provided configuration exists on the form with the name [{$formName}] on the [{$livewireClass}] component."
                );
            }

            return $this;
        };
    }

    public function assertTableColumnConfig()
    {
        return function(string $columnName, $record, \Closure $callback = null): static {
            $this->assertTableColumnExists($columnName);

            $livewire = $this->instance();
            $livewireClass = $livewire::class;

            //$column = $livewire->getCachedTableColumn($columnName);
            $column = $livewire->getTable()->getColumn($columnName);

            if (! ($record instanceof Model)) {
                $record = $livewire->getTableRecord($record);
            }

            $column->record($record);

            Assert::assertTrue(
                $callback($column),
                message: "Failed asserting that a table column with name [{$columnName}] has the given config for record [{$record->getKey()}] on the [{$livewireClass}] component.",
            );

            return $this;
        };
    }

    public function assertTableHasRecordUrl()
    {
        return function(string $url, $record) {

            $livewire = $this->instance();

            /** @var RelationManager $livewire */
            $livewire->recordAction();

            Assert::assertEquals(
                $url,
                $livewire->getRecordUrl($record),
                "The record url is no {$url}"
            );
        };
    }

    public function assertFormStructure()
    {
        return function(array $structure, string $formName = 'form'): static {

            throw new \Exception('use assertFormFieldExists instead');

            /** @phpstan-ignore-next-line */
            $this->assertFormExists($formName);

            /** @var Page $livewire */
            $livewire = $this->instance();

            /** @var ComponentContainer $form */
            $form = $livewire->{$formName};

            //main grid
            /** @var Grid $main */
            $main = $form->getComponents(withHidden: true)[0];

            $schema = $main->getChildComponents();

            $temp_container = ComponentContainer::make($livewire)
                ->parentComponent($main)
                ->schema($structure)
                ->fill();

            $structure = $temp_container->getComponents(withHidden: true);

            Assert::assertEquals(
                $structure,
                $schema,
                "Failed asserting that form '{$formName}' has the specified structure"
            );

            return $this;
        };
    }
}
