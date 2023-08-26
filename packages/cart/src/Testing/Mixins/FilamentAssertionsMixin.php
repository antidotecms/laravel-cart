<?php

namespace Antidote\LaravelCart\Testing\Mixins;

use Filament\Forms\ComponentContainer;
use Filament\Forms\Components\Field;
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
            $action = $livewire->getCachedTableAction($name) ?? $livewire->getCachedTableEmptyStateAction($name) ?? $livewire->getCachedTableHeaderAction($name);

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
            $action = $livewire->getCachedTableAction($name) ?? $livewire->getCachedTableEmptyStateAction($name) ?? $livewire->getCachedTableHeaderAction($name);

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

    public function assertFormLayoutExists() {
        return function(string $layoutName, string | \Closure $formName = 'form', ?\Closure $callback = null): static {

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
                return is_subclass_of($component, Field::class);
            });

            //remove main container
            $layouts->shift();

            $layout = $layouts->filter(function($item) use ($layoutName) {
                return $item->getHeading() == $layoutName;
            })->first();

            Assert::assertInstanceOf(
                \Filament\Support\Components\Component::class,
                $layout,
                "Failed asserting that a layout component with the name [{$layoutName}] exists on the form with the name [{$formName}] on the [{$livewireClass}] component."
            );

            if ($callback) {
                Assert::assertTrue(
                    $callback($layout),
                    "Failed asserting that a layout component with the name [{$layoutName}] and provided configuration exists on the form with the name [{$formName}] on the [{$livewireClass}] component."
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

            $column = $livewire->getCachedTableColumn($columnName);

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
}
