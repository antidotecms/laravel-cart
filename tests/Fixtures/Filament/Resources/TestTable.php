<?php

namespace Antidote\LaravelCart\Tests\Fixtures\Filament\Resources;
class TestTable extends \Livewire\Component implements \Filament\Tables\Contracts\HasTable
{
    use \Filament\Tables\Concerns\InteractsWithTable;

    public function getActions(): array
    {
        return [
            \Filament\Tables\Actions\Action::make('test_action')
                ->modalContent('some content')
        ];
    }
}
