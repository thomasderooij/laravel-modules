<?php

namespace Thomasderooij\LaravelModules\Http;

use Illuminate\Contracts\Foundation\Application;
use Illuminate\Foundation\Http\Kernel as HttpKernel;
use Illuminate\Routing\Router;
use Thomasderooij\LaravelModules\Contracts\HttpCompositeKernel;
use Thomasderooij\LaravelModules\Exceptions\InitExceptions\ConfigFileNotFoundException;
use Thomasderooij\LaravelModules\Exceptions\InitExceptions\ModulesNotInitialisedException;
use Thomasderooij\LaravelModules\Exceptions\InitExceptions\TrackerFileNotFoundException;
use Thomasderooij\LaravelModules\Services\ModuleManager;

class CompositeKernel extends HttpKernel implements HttpCompositeKernel
{
    /**
     * The application's global HTTP middleware stack.
     *
     * These middleware are run during every request to your application.
     *
     * @var array
     */
    protected $middleware = [];

    /**
     * The application's route middleware groups.
     *
     * @var array
     */
    protected $middlewareGroups = [];

    /**
     * The application's route middleware.
     *
     * These middleware may be assigned to groups or used individually.
     *
     * @var array
     */
    protected $routeMiddleware = [];

    /**
     * The priority-sorted list of middleware.
     *
     * This forces non-global middleware to always be in the given order.
     *
     * @var array
     */
    protected $middlewarePriority = [];

    /**
     * The collection bin for all the HTTP kernels in the vanilla and modules directories
     *
     * This merges all their property keys into the above defined properties
     *
     * @var array<CompositeKernel>
     */
    private $kernels = [];

    /**
     * Create a new HTTP kernel instance.
     *
     * @param Application $app
     * @param Router $router
     * @throws ConfigFileNotFoundException
     * @throws ModulesNotInitialisedException
     * @throws TrackerFileNotFoundException
     * @throws \ReflectionException
     */
    public function __construct(Application $app, Router $router)
    {
        $vanilla = "App\\Http\\CompositeKernel";
        if (class_exists($vanilla)) {
            $this->kernels[] = new $vanilla($app, $router);
        }

        foreach (ModuleManager::getActiveModules(true) as $module) {
            $className = ModuleManager::getModuleNameSpace($module) . "Http\\CompositeKernel";

            // Check if the module has the standard kernel, and add it to the kernel list if it exists
            if (class_exists($className)) {
                $this->kernels[] = new $className($app, $router);
            }
        }

        $this->resolveProperties();

        parent::__construct($app, $router);
    }

    /**
     * @throws \ReflectionException
     */
    protected function resolveProperties () : void
    {
        /** @var HttpKernel $kernel */
        foreach ($this->kernels as $kernel) {
            $reflection = new \ReflectionClass(get_class($kernel));

            foreach ($this->getPropNames() as $propName) {
                $reflectionProperty = $reflection->getProperty($propName);
                $reflectionProperty->setAccessible(true);
                $this->{$propName}[] = $reflectionProperty->getValue($kernel);
            }
        }
    }

    protected function getPropNames () : array
    {
        return [
            "middleware",
            "middlewareGroups",
            "routeMiddleware",
            "middlewarePriority"
        ];
    }
}
