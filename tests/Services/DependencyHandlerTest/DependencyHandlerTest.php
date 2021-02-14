<?php

declare(strict_types=1);

namespace Thomasderooij\LaravelModules\Tests\Services\DependencyHandlerTest;

use Mockery;
use Thomasderooij\LaravelModules\Services\ModuleManager;
use Thomasderooij\LaravelModules\Tests\Test;

abstract class DependencyHandlerTest extends Test
{
    protected $moduleManager;

    protected function setUp(): void
    {
        parent::setUp();

        // We're going to need the module manager for this
        $this->moduleManager = Mockery::mock(ModuleManager::class);
        $this->instance("module.service.manager", $this->moduleManager);
    }
}
