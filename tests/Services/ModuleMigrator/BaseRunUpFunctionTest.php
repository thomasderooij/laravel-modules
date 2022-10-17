<?php

declare(strict_types=1);

namespace Thomasderooij\LaravelModules\Tests\Services\ModuleMigrator;

use Mockery;

class BaseRunUpFunctionTest extends ModuleMigratorTest
{
    protected function setUp(): void
    {
        $this->method = "baseRunUpFunction";

        parent::setUp();
    }

    public function testWithModule(): void
    {
        // If there is no module
        $module = null;

        // We should get the migration name
        $this->migrator->shouldReceive("resolvePath")->withArgs([$file = "filename"])->andReturn($file);
        $this->migrator->shouldReceive("getMigrationName")->withArgs([$file])->andReturn($file);
        // It should notify us of the migration
        $this->migrator->shouldReceive("write")->andReturn("<comment>Migrating:</comment> {$file}");
        // Run it
        $this->migrator->shouldReceive("runMigration")->withArgs([$file, "up"]);
        // Log it
        $this->migrator->shouldReceive("logInRepository")->withArgs([$file, $batch = 42, $module]);
        // And let us know when it is done. This take the any-argument, since microtime is involved.
        $this->migrator->shouldReceive("note")->withArgs([Mockery::any()]);

        $this->uut->invoke($this->migrator, $file, $batch, false, $module);

        $this->assertTrue(true);
    }

    public function testWithoutModule(): void
    {
        // If there is a module
        $module = "TestModule";

        // We should get the migration name
        $this->migrator->shouldReceive("resolvePath")->withArgs([$file = "filename"])->andReturn($file);
        $this->migrator->shouldReceive("getMigrationName")->withArgs([$file])->andReturn($file);
        // It should notify us of the migration
        $this->migrator->shouldReceive("write")->andReturn("<comment>Migrating:</comment> {$file}");
        // Run it
        $this->migrator->shouldReceive("runMigration")->withArgs([$file, "up"]);
        // Log it
        $this->migrator->shouldReceive("logInRepository")->withArgs([$file, $batch = 42, $module]);
        // And let us know when it is done. This take the any-argument, since microtime is involved.
        $this->migrator->shouldReceive("note")->withArgs([Mockery::any()]);

        $this->uut->invoke($this->migrator, $file, $batch, false, $module);

        $this->assertTrue(true);
    }
}
