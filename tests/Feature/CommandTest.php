<?php

declare(strict_types=1);

namespace Thomasderooij\LaravelModules\Tests\Feature;

use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Facades\Config;
use Mockery;
use Thomasderooij\LaravelModules\Tests\Test;

abstract class CommandTest extends Test
{
    protected $filesystem;

    protected function setUp(): void
    {
        parent::setUp();

        $this->filesystem = Mockery::mock(Filesystem::class);
        $this->instance("files", $this->filesystem);

        Config::shouldReceive("offsetGet")->withArgs(["app.timezone"])->andReturn("UTC");
        Config::shouldReceive("offsetGet")->withArgs(["cache.default"])->andReturn($driver = "file");
        Config::shouldReceive("offsetGet")->withArgs(["cache.stores.file"])->andReturn([
            'driver' => 'file',
            'path' => storage_path('framework/cache/data')
        ]);
        Config::shouldReceive("offsetGet")->withArgs(["database.migrations"])->andReturn("migrations");
    }
}
