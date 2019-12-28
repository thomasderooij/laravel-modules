<?php

namespace Thomasderooij\LaravelModules\Console\Commands;

use Thomasderooij\LaravelModules\Console\Commands\Extensions\ModulesCommandTrait;
use Thomasderooij\LaravelTests\Console\Commands\TestCommand;

class TestModuleCommand extends TestCommand
{
    use ModulesCommandTrait;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'module:test {--module= : The modules you want to test. Defaults to your workbench }
                    {--tests= : The test frameworks you want to use }
                    {--dir= : The directory you want to test }';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Run your tests';

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle()
    {
        $this->getModules();

        $this->phpUnit->run();
    }


}
