<?php

declare(strict_types=1);

namespace Thomasderooij\LaravelModules\Tests\Services\DependencyHandlerTest;

class GetDependenciesKeyTest extends DependencyHandlerTest
{
    protected string $method = "getDependenciesKey";

    public function testGetDependenciesKey () : void
    {
        // The key should be "dependencies"
        $this->assertSame("dependencies", $this->uut->invoke($this->methodHandler));
    }
}
