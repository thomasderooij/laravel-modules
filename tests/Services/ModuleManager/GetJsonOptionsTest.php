<?php

declare(strict_types=1);

namespace Thomasderooij\LaravelModules\Tests\Services\ModuleManager;

use Thomasderooij\LaravelModules\Services\ModuleManager;

class GetJsonOptionsTest extends ModuleManagerTest
{
    private $method = "getJsonOptions";

    /**
     * Here we test the default value of the json options. We do this because we want to this test to fail when
     *  this value gets changed and breaks backward compatibility.
     */
    public function testGetJsonOptions () : void
    {
        // If I have a method
        $reflection = new \ReflectionClass(ModuleManager::class);
        $uut = $reflection->getMethod($this->method);
        $uut->setAccessible(true);

        // I expect the active modules tracker key to be returned
        $moduleManager = $this->getMockManager(null, $this->method);
        $expected = [JSON_PRETTY_PRINT, JSON_UNESCAPED_SLASHES];
        $this->assertSame($expected, $uut->invoke($moduleManager));
    }
}
