<?php

declare(strict_types=1);

namespace Thomasderooij\LaravelModules\Tests\Services\ModuleMigrator;

class RunUpTest extends ModuleMigratorTest
{
    public function setUp(): void
    {
        $this->method = "runUp";

        parent::setUp();
    }

    public function testWithoutModule(): void
    {
        // If there is no module
        $module = null;

        // The parent function should be called
        $file = "file";
        $batch = 42;
        $pretend = false;
        $this->migrator->shouldReceive("parentCall")->withArgs([$this->method, $args = [$file, $batch, $pretend]]);

        $args[] = $module;
        $this->uut->invoke($this->migrator, ...$args);
    }

    public function testWithModule(): void
    {
        // If there is a module
        $module = "SomeModule";

        // The base run up function should be called
        $file = "file";
        $batch = 42;
        $pretend = false;
        $this->migrator->shouldReceive("baseRunUpFunction")->withArgs($args = [$file, $batch, $pretend, $module]);

        $this->uut->invoke($this->migrator, ...$args);
    }
}
