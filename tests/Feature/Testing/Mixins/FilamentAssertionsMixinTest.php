<?php

use Antidote\LaravelCart\Tests\Fixtures\Filament\Resources\TestTable;

it('will assert that a table action exists with the given modal content', function() {

    $this->markTestIncomplete('to do');

    \Pest\Livewire\livewire(TestTable::class)
        ->assertTableActionExists('test_action');

//    \Illuminate\Support\Facades\View::shouldReceive('make')
//        ->with('livewire.test-tablle')
//        ->withAnyArgs()
//        ->once();

   //dump(app('view.finder'));

//    $mock_view_finder = $this->mock(\Illuminate\View\FileViewFinder::class, function(\Mockery\MockInterface $mock) {
//        $mock->shouldReceive('find')
//            ->andReturn(fn() => \Mockery::self())
//            ->once();
//    });

//    $mock_filesystem = $this->mock(Illuminate\Filesystem\Filesystem::class, function(\Mockery\MockInterface $mock) {
//        $fake_disk = \Illuminate\Support\Facades\Storage::fake();
//        $fake_disk->put('/livewire/test-table.blade.php', 'some content');
//        return $fake_disk;
//    });

//    $mock_view_finder = $this->mock(\Illuminate\View\FileViewFinder::class, function(\Mockery\MockInterface $mock) {
//        $fake_disk = \Illuminate\Support\Facades\Storage::fake();
//        $fake_disk->put('/views/livewire/test-table.blade.php', 'some content');
//        $fake_filesystem = new \Antidote\LaravelCart\Tests\Feature\Testing\FakeFileSystem();
//        $fake_filesystem->disk($fake_disk);
//        $file_view_finder = new \Illuminate\View\FileViewFinder($fake_filesystem, ['/views'], ['blade.php']);
//        return $file_view_finder;
//    })->makePartial();

//    $config = app('config');
//    $config->set('livewire.view_path', '/views/livewire');
//    $config->set('view.paths', ['/views']);
//    //dump($config['livewire']);
//    //dump($config['view']);
//
//    $fake_disk = \Illuminate\Support\Facades\Storage::fake();
//    $fake_disk->put('/views/livewire/test-table.blade.php', '<div>{{ $this->table }}</div>');
//    $fake_disk->put(
//        '/var/www/html/vendor/livewire/livewire/src/views/mount-component.blade.php',
//        file_get_contents('/var/www/html/vendor/livewire/livewire/src/Testing/../views/mount-component.blade.php')
//    );
//    $fake_filesystem = new \Antidote\LaravelCart\Tests\Feature\Testing\FakeFileSystem();
//    $fake_filesystem->disk($fake_disk);
//    $mock_view_finder = new \Illuminate\View\FileViewFinder($fake_filesystem, ['/views'], ['blade.php']);
//
//    app()->bind('view.finder', fn() => $mock_view_finder);
//    app()->singleton('blade.compiler', function() use ($fake_filesystem) {
//        $config = app('config');
//        return new \Illuminate\View\Compilers\BladeCompiler(
//            $fake_filesystem,
//            $config['view.compiled'],
//            $config->get('view.relative_hash', false) ? $app->basePath() : '',
//            $config->get('view.cache', true),
//            $config->get('view.compiled_extension', 'php')
//
//        );
//    });
//
//    //dump(app('view.finder'));
//
//    \Pest\Livewire\livewire(TestTable::class)
//        ->assertTableActionExists('test_action');
});
