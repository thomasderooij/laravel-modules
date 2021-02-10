<?php

declare(strict_types=1);

namespace Thomasderooij\LaravelModules\Tests\Services\ModuleMigrationRepository;

class LogTest extends ModuleMigrationRepositoryTest
{
    protected function setUp(): void
    {
        $this->method = "log";

        parent::setUp();
    }

    public function testLogWithoutModule () : void
    {
        // If there is no module
        $module = null;

        $this->repository->shouldReceive("parentCall")->withArgs([$this->method, [$file = "file", $batch = 1]])->once();

        $this->uut->invoke($this->repository, $file, $batch, $module);
    }

    public function testLogWithModule () : void
    {
        // If I have a module
        $module = "MyModule";
        $this->repository->shouldReceive("table")->andReturn($this->builder);

        $record = ["migration" => $file = "file", "batch" => $batch = 1, "module" => $module];
        $this->builder->shouldReceive("insert")->withArgs([$record]);

        $this->uut->invoke($this->repository, $file, $batch, $module);
    }
}
