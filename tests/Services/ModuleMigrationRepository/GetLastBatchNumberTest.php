<?php

declare(strict_types=1);

namespace Thomasderooij\LaravelModules\Tests\Services\ModuleMigrationRepository;

class GetLastBatchNumberTest extends ModuleMigrationRepositoryTest
{
    protected function setUp(): void
    {
        $this->method = "getLastBatchNumber";

        parent::setUp();
    }

    public function testWithoutModule(): void
    {
        // If there is no module
        $module = null;

        // I expect the parent call to be made
        $this->repository->shouldReceive("parentCall")->withArgs([$this->method])->andReturn($expected = 109);

        // I expect this parent call to be returned
        $result = $this->uut->invoke($this->repository, $module);
        $this->assertSame($expected, $result);
    }

    public function testWithModule(): void
    {
        // If I have a module
        $module = "ThisModule";

        // I expect the builder to filter for the module
        $this->repository->shouldReceive("table")->andReturn($this->builder);
        $this->builder->shouldReceive("where")->withArgs(["module", "=", $module])->andReturnSelf();
        // And then to fetch the highest
        $this->builder->shouldReceive("max")->andReturn($expected = 107);

        // And this should be returned
        $result = $this->uut->invoke($this->repository, $module);
        $this->assertSame($expected, $result);
    }
}
