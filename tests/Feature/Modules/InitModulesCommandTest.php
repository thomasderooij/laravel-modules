<?php

declare(strict_types=1);

namespace Thomasderooij\LaravelModules\Tests\Feature\Modules;

use Illuminate\Support\Composer;
use Mockery;
use Thomasderooij\LaravelModules\Console\Commands\InitModuleCommand;
use Thomasderooij\LaravelModules\Contracts\Factories\AppBootstrapFactory as AppBootstrapFactoryContract;
use Thomasderooij\LaravelModules\Contracts\Factories\ConfigFactory as ConfigFactoryContract;
use Thomasderooij\LaravelModules\Contracts\Factories\ModuleMigrationFactory as ModuleMigrationFactoryContract;
use Thomasderooij\LaravelModules\Contracts\Services\ModuleManager as ModuleManagerContract;
use Thomasderooij\LaravelModules\Factories\AppBootstrapFactory;
use Thomasderooij\LaravelModules\Factories\ConfigFactory;
use Thomasderooij\LaravelModules\Factories\ModuleMigrationFactory;
use Thomasderooij\LaravelModules\Services\ModuleManager;
use Thomasderooij\LaravelModules\Tests\Test;

class InitModulesCommandTest extends Test
{

}
