<?php

namespace Thomasderooij\LaravelModules\Console\Commands;

use Illuminate\Console\Command;
use Thomasderooij\LaravelModules\Contracts\Services\ModuleManager;

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

    public function handle ()
    {
        if ($this->moduleManager->getWorkbench() === null) {
            $this->info("Your workbench is empty.");
        } else {
            $this->info($this->moduleManager->getWorkbench());
        }
    }
}
