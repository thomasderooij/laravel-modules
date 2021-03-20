<?php

declare(strict_types=1);

namespace Thomasderooij\LaravelModules\Console\Commands;

class UnsetWorkbenchCommand extends ModuleCommand
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'module:unset';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Clear your currently active workbench and return to the default Laravel commands';

    /**
     * Handle the workbench clearing
     */
    public function handle ()
    {
        if (!$this->moduleManager->isInitialised()) {
            $this->displayInitialisationError();
            return false;
        }

        $this->moduleManager->clearWorkbench();

        $this->displayConfirmationMessage();
    }

    /**
     * Display a notice of successfully having cleared the workbench
     */
    protected function displayConfirmationMessage () : void
    {
        $this->info("Your workbench has been cleared.");
    }
}
