<?php

declare(strict_types=1);

namespace Thomasderooij\LaravelModules\Tests\Services\ModuleMigrator;

class LogInRepositoryTest extends ModuleMigratorTest
{
    protected function setUp(): void
    {
        $this->method = "logInRepository";

        parent::setUp();
    }

    public function testWithModule(): void
    {
        // If there is no module
        $module = null;

        // The parent call should be made
        $this->repository->shouldReceive("log")->withArgs([$name = "migration", $batch = 42]);

        // And the should be the end of it
        $this->uut->invoke($this->migrator, $name, $batch, $module);
    }

    public function testWithoutModule(): void
    {
        // If there is a module
        $module = "TestModule";

        // The parent call should be made
        $this->repository->shouldReceive("log")->withArgs([$name = "migration", $batch = 42, $module]);

        // And the should be the end of it
        $this->uut->invoke($this->migrator, $name, $batch, $module);
    }
}
