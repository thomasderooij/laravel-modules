<?php

declare(strict_types=1);

namespace Thomasderooij\LaravelModules\Console\Commands;

use Thomasderooij\LaravelModules\Contracts\Services\DependencyHandler;
use Thomasderooij\LaravelModules\Contracts\Services\ModuleManager;

class DeleteDependencyModuleCommand extends ModuleCommand
{
    protected $dependencyHandler;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'module:delete-dependency {name}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = "Remove dependencies from a module";

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

        // We ask which module to remove
        $response = $this->askForModule($module, $this->dependencyHandler->getAvailableModules($module), false);
        if ($response !== "None. I changed my mind") {
            $this->dependencyHandler->removeDependency($module, $response);

            // If a module has been removed, we ask for the next, until indicate the process is done
            while (($response = $this->askForModule($module, $this->dependencyHandler->getAvailableModules($module), true)) !== "No, I'm done removing dependencies") {
                $this->dependencyHandler->removeDependency($module, $response);
            }
        }

        // And then we give a confirmation
        $this->giveConfirmation();
    }

    protected function askForModule (string $module, array $modules, bool $followUp) : string
    {
        $options = [($followUp ? "No, I'm done removing dependencies" : "None. I changed my mind")];
        $options = array_merge($options, $modules);

        $firstQuestion = "Which module do you want to remove from \"$module\"?";
        $followUpQuestion = "Done. Would you like to remove another one from \"$module\"?";

        return $this->choice($followUp ? $followUpQuestion : $firstQuestion, $options);
    }

    protected function giveConfirmation () : void
    {
        $this->info("Alright. Your module dependencies have been updated.");
    }
}
