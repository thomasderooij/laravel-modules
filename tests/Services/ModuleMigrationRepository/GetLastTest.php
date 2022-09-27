<?php

declare(strict_types=1);

namespace Thomasderooij\LaravelModules\Tests\Services\ModuleMigrationRepository;

class GetLastTest extends ModuleMigrationRepositoryTest
{
    protected function setUp(): void
    {
        $this->method = "getLast";

        parent::setUp();
    }

    public function testGetLastWithoutModule(): void
    {
        // If there is no module
        $module = null;

        // The parent call should be made
        $this->repository->shouldReceive("parentCall")->withArgs([$this->method])->andReturn(
            $expected = ["return", "value"]
        );

        // And that value should be returned
        $result = $this->uut->invoke($this->repository, $module);
        $this->assertSame($expected, $result);
    }

    public function testGetLastWithModule(): void
    {
        // If there is a module
        $module = "MyModule";

        // We should know the last batch number
        $this->repository->shouldReceive("getLastBatchNumber")->withArgs([$module])->andReturn($lastBatchNumber = 42);
        $this->repository->shouldReceive("table")->andReturn($this->builder);

        // The builder should filter for the module
        $this->builder->shouldReceive("where")->withArgs([
            [
                ['batch', "=", $lastBatchNumber],
                ["module", "=", $module]
            ]
        ])->andReturnSelf();
        $this->builder->shouldReceive("orderBy")->withArgs(["migration", "desc"])->andReturnSelf();
        $this->builder->shouldReceive("get")->andReturn(collect($expected = ["expected", "result"]));

        // We expect to get an array of results
        $result = $this->uut->invoke($this->repository, $module);
        $this->assertSame($expected, $result);
    }
}
