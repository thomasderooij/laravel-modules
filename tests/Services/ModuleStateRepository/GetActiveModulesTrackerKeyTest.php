<?php

declare(strict_types=1);

namespace Thomasderooij\LaravelModules\Tests\Services\ModuleStateRepository;

class GetActiveModulesTrackerKeyTest extends ModuleStateRepositoryTest
{
    private $method = "getActiveModulesTrackerKey";

    /**
     * Here we test the default value of the modules tracker key. We do this because we want to this test to fail when
     *  this value gets changed and breaks backward compatibility.
     */
    public function testGetActiveModulesTrackerKey () : void
    {
        // If I have a method
        $uut = $this->getMethod($this->method);

        // I expect the active modules tracker key to be returned
        $moduleManager = $this->getMockRepository($this->method);
        $expected = "activeModules";
        $this->assertSame($expected, $uut->invoke($moduleManager));
    }
}
