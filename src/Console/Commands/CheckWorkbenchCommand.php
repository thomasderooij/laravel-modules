<?php

declare(strict_types=1);

namespace Thomasderooij\LaravelModules\Console\Commands;

class CheckWorkbenchCommand extends ModuleCommand
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'module:check';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Check your currently active module';

    public function handle(): void
    {
        if (!$this->moduleManager->isInitialised()) {
            $this->displayInitialisationError();
            return;
        }

        if ($this->moduleManager->getWorkbench() === null) {
            $this->info("Your workbench is empty.");
        } else {
            $this->info($this->moduleManager->getWorkbench());
        }
    }
}
