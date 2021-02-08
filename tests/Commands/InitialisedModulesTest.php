<?php

declare(strict_types=1);

namespace Thomasderooij\LaravelModules\Tests\Commands;

use Thomasderooij\LaravelModules\ConsoleSupportServiceProvider;
use Thomasderooij\LaravelModules\ModuleServiceProvider;
use Thomasderooij\LaravelModules\Tests\Test;

class InitialisedModulesTest extends Test
{
    public function getApplicationProviders($app) : array
    {
        $providers = parent::getApplicationProviders($app);

        return $providers;
    }
}
