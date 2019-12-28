<?php

namespace Thomasderooij\LaravelModules\Console;

use App\Console\Kernel;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Contracts\Events\Dispatcher;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use Thomasderooij\LaravelModules\Contracts\ConsoleCompositeKernel;
use Thomasderooij\LaravelModules\Exceptions\InitExceptions\ConfigFileNotFoundException;
use Thomasderooij\LaravelModules\Exceptions\InitExceptions\ModulesNotInitialisedException;
use Thomasderooij\LaravelModules\Exceptions\InitExceptions\TrackerFileNotFoundException;
use Thomasderooij\LaravelModules\Services\ModuleManager;

class CompositeKernel extends ConsoleKernel implements ConsoleCompositeKernel
{
    /**
     * An array of kernels present in the active modules
     *
     * @var array
     */
    protected $kernels;

    public function __construct(Application $app, Dispatcher $events)
    {
        // Have the parent set the app to its private properties
        parent::__construct($app, $events);

        // Once the app is booted, compile the kernels we want to manage
        $app->booted(function (Application $app) use ($events) {
            $this->kernels = [];

            // Include the default kernel if it still exists
            if (class_exists(Kernel::class)) {
                $this->kernels[] = new Kernel($app, $events);
            }

            $this->activeModulesToKernels($app, $events);
        });
    }

    /**
     * Collect all the kernels from the modules and add them to the kernels property
     *
     * @param Application $app
     * @param Dispatcher $events
     * @throws ConfigFileNotFoundException
     * @throws ModulesNotInitialisedException
     * @throws TrackerFileNotFoundException
     */
    protected function activeModulesToKernels (Application $app, Dispatcher $events) : void
    {
        foreach (ModuleManager::getActiveModules(true) as $module) {
            $className = ModuleManager::getModuleNameSpace($module) . "Console\\Kernel";

            // Check if the module has the standard kernel, and add it to the kernel list if it exists
            if (class_exists($className)) {
                $this->kernels[] = new $className($app, $events);
            }
        }
    }

    /**
     * Invoke the schedule function on all of the kernels
     *
     * @param Schedule $schedule
     * @throws \ReflectionException
     */
    protected function schedule(Schedule $schedule)
    {
        foreach ($this->kernels as $kernel) {
            $reflection = new \ReflectionClass(get_class($kernel));
            $method = $reflection->getMethod('schedule');
            $method->setAccessible(true);

            $method->invoke($kernel, $schedule);
        }
    }

    /**
     * Invoke the commands function on all of the kernels
     *
     * @throws \ReflectionException
     */
    protected function commands()
    {
        foreach ($this->kernels as $kernel) {
            $reflection = new \ReflectionClass(get_class($kernel));
            $method = $reflection->getMethod('commands');
            $method->setAccessible(true);

            $method->invoke($kernel);
        }
    }
}
