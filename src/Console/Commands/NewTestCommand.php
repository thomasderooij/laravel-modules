<?php

namespace Thomasderooij\LaravelModules\Console\Commands;

use Illuminate\Console\Command;
use Thomasderooij\LaravelModules\Contracts\Services\ModuleManager;

class NewTestCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'module:new-test
                    { test : the type of test you want to create }
                    { name : The name of your test }
                    {--module= : The module in which you want to create a test }
                    {--options= : Further options to specify your test }';

    /**
     * The test frameworks that are available for creation
     *
     * @var array
     */
    protected $tests = [
        "phpunit",
    ];

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new module for your project';

    /**
     * @var ModuleManager $manager
     */
    protected $manager;

    public function __construct(ModuleManager $manager)
    {
        parent::__construct();

        $this->manager = $manager;
    }

    public function handle()
    {
        if (($type = $this->getTest()) === null) return;
        $name = $this->getTestName();
        $module = $this->getModule();
        $options = $this->getMiscOptions();
    }

    /**
     * Return the test type or null
     *
     * @return string|null
     */
    protected function getTest ()
    {
        $test = $this->getTestArgument();

        if (!in_array($test, $this->tests)) {
            $this->displayTestNotAvailableWarning($test);

            return null;
        }

        return $test;
    }

    protected function getTestArgument () : string
    {
        return strtolower($this->argument("test"));
    }

    protected function displayTestNotAvailableWarning (string $test) : void
    {
        $tests = $this->getAvailableTestsString();
        $this->warn("\"$test\" is not available. You can choose between the following tests: $tests");
    }

    protected function getAvailableTestsString () : string
    {
        $tests = array_map(function (string $test) { return ucfirst($test); }, $this->tests);

        return implode(", ", $tests);
    }

    protected function getTestName () : string
    {
        return $this->argument("name");
    }

    /**
     * Get the module from the argument. If no argument is provided, return the workbench.
     *
     * @return string|null
     */
    protected function getModule ()
    {
        $module = $this->argument("module") ?: null;

        if ($module !== null) {
            return $module;
        }

        return $this->manager->getWorkbench();
    }

    protected function getMiscOptions () : array
    {

    }
}
