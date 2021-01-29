<?php

declare(strict_types=1);

namespace Thomasderooij\LaravelModules\Tests\Unit\ModuleManager;

use Illuminate\Support\Facades\Config;
use Thomasderooij\LaravelModules\Contracts\Services\ModuleManager;

class IsInitialisedTest extends ModuleManagerTest
{
    public function testHasConfig () : void
    {
//        Config::shouldReceive('get')->andReturn([]);

        /** @var ModuleManager $uut */
        $uut = $this->app->make('module.service.manager');

//        $this->assertTrue();
    }
}
