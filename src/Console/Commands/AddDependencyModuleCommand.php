<?php

declare(strict_types=1);

namespace Thomasderooij\LaravelModules\Console\Commands;

use Thomasderooij\LaravelModules\Contracts\Services\DependencyHandler;
use Thomasderooij\LaravelModules\Contracts\Services\ModuleManager;

class AddDependencyModuleCommand extends ModuleCommand
{
    protected $dependencyHandler;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'module:add-dependency {name}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Add dependencies to a module';

    public function __construct(ModuleManager $moduleManager, DependencyHandler $dependencyHandler)
    {
        parent::__construct($moduleManager);

        $this->dependencyHandler = $dependencyHandler;
    }

    public function handle()
    {
        $module = $this->getNameArgument();

        // We check if our modules are properly set up
        if (!$this->passesCheck($module)) {
            return;
        }

        // We keep track of how many times we asked this questions
        $i = 1;
        // We call the getAvailableModules function here every time, since the previous iteration changes the current options
        while (($dependency = $this->askForModule($module, $this->dependencyHandler->getAvailableModules($module), $i)) !== "None. I'm done here.") {
            $this->dependencyHandler->addDependency($module, $dependency);
            $i++;
        }

        $this->giveConfirmation();
    }

    protected function askForModule (string $module, array $modules, int $loop) : string
    {
        $options = ["None. I'm done here."];
        foreach ($modules as $option) {
            $options[] = $option;
        }

        $firstquestion = "Which module is \"$module\" dependent on?";
        $followUp = "Alright. I've added it. What other module is \"$module\" dependent on?";
        return $this->choice($loop > 1 ? $followUp : $firstquestion, $options);
    }

    protected function giveConfirmation () : void
    {
        $this->info("Roger that.");
    }
}
