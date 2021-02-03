<?php

declare(strict_types=1);

namespace Thomasderooij\LaravelModules\Console\Commands;

use Thomasderooij\LaravelModules\Contracts\Services\ModuleManager;

class DeleteModuleCommand extends ModuleCommand
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'module:delete {name}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Run the laravel_tests in this module';

    public function __construct (ModuleManager $manager)
    {
        parent::__construct($manager);
    }

    public function handle()
    {
        $name = $this->getNameArgument();

        if (!$this->passesCheck($name)) {
            return;
        }

        $answer = $this->askConfirmation($name);
        if ($this->isConfirmed($answer)) {
            $this->moduleManager->removeModule($name);
            $this->confirmDeletion();
        } elseif ($this->isCancellation($answer)) {
            $this->confirmCancellation();
        }
    }

    protected function isConfirmed (string $answer) : bool
    {
        return $answer === $this->getConfirmationOptions()[1];
    }

    protected function isCancellation (string $answer) : bool
    {
        return $answer === $this->getConfirmationOptions()[0];
    }

    protected function askConfirmation (string $module) : string
    {
        return $this->choice("This will delete your module \"$module\" and all of the code within it. Are you sure you want to do this?", $this->getConfirmationOptions());
    }

    protected function getConfirmationOptions () : array
    {
        return [
            1 => "Yes, I'm sure",
            0 => "No, I don't want to delete everything",
        ];
    }

    protected function confirmDeletion () : void
    {
        $this->warn("Aaaaaand it's gone.");
    }

    protected function confirmCancellation () : void
    {
        $this->warn("Gotcha. I'll leave your code intact.");
    }
}
