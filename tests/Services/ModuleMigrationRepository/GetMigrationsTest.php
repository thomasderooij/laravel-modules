<?php

declare(strict_types=1);

namespace Thomasderooij\LaravelModules\Tests\Services\ModuleMigrationRepository;

class GetMigrationsTest extends ModuleMigrationRepositoryTest
{
    protected function setUp(): void
    {
        $this->method = "getMigrations";

        parent::setUp();
    }

    public function testGetMigrationsWithoutModule () : void
    {
        // If there is no module
        $module = null;

        // The default behaviour should occur
        $this->repository->shouldReceive("parentCall")->withArgs([$this->method, [$steps = 1]])->andReturn($expected = ["result"]);

        // And this should be returned by the function
        $result = $this->uut->invoke($this->repository, $steps, $module);
        $this->assertSame($expected, $result);
    }

    public function testGetMigrationsWithModule () : void
    {
        // If there is no module
        $module = "MyModulle";
        $this->repository->shouldReceive("table")->andReturn($this->builder);

        // The builder should setup a query filter
        $this->builder->shouldReceive("where")->withArgs([[
            ['batch', '>=', '1'],
            ["module", "=", $module]
        ]])->andReturn($this->builder);

        // Setup sorting
        $steps = 1;
        $this->builder->shouldReceive("orderBy")->withArgs(["batch", "desc"])->andReturnSelf();
        $this->builder->shouldReceive("orderBy")->withArgs(["migration", "desc"])->andReturnSelf();
        $this->builder->shouldReceive("take")->withArgs([$steps])->andReturnSelf();
        $this->builder->shouldReceive("get")->andReturn(collect($expected = ["content"]));

        // And this should be returned by the function
        $result = $this->uut->invoke($this->repository, $steps, $module);
        $this->assertSame($expected, $result);
    }
}
