<?php

declare(strict_types=1);

namespace Thomasderooij\LaravelModules\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Contracts\Events\Dispatcher;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use ReflectionException;
use Thomasderooij\LaravelModules\Contracts\ConsoleCompositeKernel;
use Thomasderooij\LaravelModules\Contracts\Services\ModuleManager;

class CompositeKernel extends ConsoleKernel implements ConsoleCompositeKernel
{
    /**
     * An array of kernels present in the active modules
     */
    protected array $kernels;

    public function __construct(Application $app, Dispatcher $events)
    {
        // If the instance class is not the class of the composite kernel, call the parents
        if (static::class !== self::class) {
            return parent::__construct($app, $events);
        }

        // Once the app is booted, compile the kernels we want to manage
        $app->booted(function (Application $app) use ($events) {
            $this->kernels = [];

            // Include the default kernel if it still exists
            $class = "App\Console\Kernel";
            if (class_exists($class)) {
                $this->kernels[] = new $class($app, $events);
            }

            $this->activeModulesToKernels($app, $events);
        });

        // Have the parent set the app to its private properties
        parent::__construct($app, $events);
    }

    /**
     * Collect all the kernels from the modules and add them to the kernels property
     *
     * @param Application $app
     * @param Dispatcher $events
     * @throws BindingResolutionException
     */
    protected function activeModulesToKernels(Application $app, Dispatcher $events): void
    {
        /** @var ModuleManager $moduleManager */
        $moduleManager = $app->make(\Thomasderooij\LaravelModules\Services\ModuleManager::class);
        foreach ($moduleManager->getActiveModules(true) as $module) {
            $className = $moduleManager->getModuleNamespace($module) . "Console\\Kernel";

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
     * @throws ReflectionException
     */
    protected function schedule(Schedule $schedule): void
    {
        foreach ($this->kernels as $kernel) {
            $reflection = new \ReflectionClass(get_class($kernel));
            $method = $reflection->getMethod('schedule');

            $method->invoke($kernel, $schedule);
        }
    }

    /**
     * Invoke the commands function on all of the kernels
     *
     * @throws ReflectionException
     */
    protected function commands()
    {
        foreach ($this->kernels as $kernel) {
            $reflection = new \ReflectionClass(get_class($kernel));
            $method = $reflection->getMethod('commands');

            $method->invoke($kernel);
        }
    }
}
