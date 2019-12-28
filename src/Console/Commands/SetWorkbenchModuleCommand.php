<?php

namespace Thomasderooij\LaravelModules\Console\Commands;

class SetWorkbenchModuleCommand extends ModuleCommand
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'module:set {name}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Set a module as your current workbench';

    public function handle()
    {
        $module = $this->getNameArgument();

        if (!$this->passesCheck($module)) {
            return;
        }

        $this->moduleManager->setWorkbench($module);

        $this->displayConfirmationMessage($module);
    }

    /**
     * Display a notice of having set the module to the workbench
     *
     * @param string $module
     */
    protected function displayConfirmationMessage (string $module) : void
    {
        $this->info("The module $module is now set to your workbench.");
    }
}
