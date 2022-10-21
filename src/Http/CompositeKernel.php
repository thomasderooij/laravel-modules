<?php

declare(strict_types=1);

namespace Thomasderooij\LaravelModules\Http;

use Illuminate\Contracts\Foundation\Application;
use Illuminate\Foundation\Http\Kernel as HttpKernel;
use Illuminate\Routing\Router;
use ReflectionClass;
use ReflectionException;
use Thomasderooij\LaravelModules\Contracts\HttpCompositeKernel;
use Thomasderooij\LaravelModules\Contracts\Services\ModuleManager;

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
    private array $kernels = [];

    /**
     * Create a new HTTP kernel instance.
     *
     * @param Application $app
     * @param Router $router
     */
    public function __construct(Application $app, Router $router)
    {
        // If the instance class is not the class of the composite kernel, call the parents
        if (static::class !== self::class) {
            return parent::__construct($app, $router);
        }

        $vanilla = config('modules.app_namespace', 'App') . "\\Http\\Kernel";
        if (class_exists($vanilla)) {
            $this->kernels[] = new $vanilla($app, $router);
        }

        $app->booted(function (Application $application) use ($router) {
            /** @var ModuleManager $moduleManager */
            $moduleManager = $application->make(\Thomasderooij\LaravelModules\Services\ModuleManager::class);

            foreach ($moduleManager->getActiveModules(true) as $module) {
                $className = $moduleManager->getModuleNamespace($module) . "Http\\Kernel";

                // Check if the module has the standard kernel, and add it to the kernel list if it exists
                if (class_exists($className)) {
                    $this->kernels[] = new $className($application, $router);
                }
            }

            $this->resolveProperties();
        });

        parent::__construct($app, $router);
    }

    /**
     * @throws ReflectionException
     */
    protected function resolveProperties () : void
    {
        foreach ($this->kernels as $kernel) {
            $reflection = new ReflectionClass(get_class($kernel));

            foreach ($this->getPropNames() as $propName) {
                $reflectionProperty = $reflection->getProperty($propName);
                $reflectionProperty->setAccessible(true);
                $this->{$propName} = array_merge($this->{$propName}, $reflectionProperty->getValue($kernel));
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
