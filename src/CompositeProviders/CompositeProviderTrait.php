<?php

declare(strict_types=1);

namespace Thomasderooij\LaravelModules\CompositeProviders;

use Illuminate\Filesystem\Filesystem;
use Thomasderooij\LaravelModules\Contracts\Services\ModuleManager;

trait CompositeProviderTrait
{
    protected $providers = [];

    /**
     * CompositeProviderTrait constructor.
     *
     * @param $app
     */
    public function __construct($app)
    {
        // Make an empty array for the prover classes
        $providers = [];

        // Check if the vanilla provider exists. If so, add it to the list of providers.
        if (class_exists("App\\Providers\\{$this->name}")) {
            $providers[] = "App\\Providers\\{$this->name}";
        }

        // Get all the active modules
        /** @var ModuleManager $moduleManager */
        $moduleManager = new \Thomasderooij\LaravelModules\Services\ModuleManager(new Filesystem());

        foreach ($moduleManager->getActiveModules(true) as $module) {
            // Check if the module has the expected provider. If so, add it to the list of providers
            $providerClass = $moduleManager->getModuleNamespace($module) . "Providers\\{$this->name}";
            if (class_exists($providerClass)) {
                $providers[] = $providerClass;
            }
        }

        // Set the providers to the class providers property
        $this->providers = $providers;

        // Continue business as per usual
        parent::__construct($app);
    }
}
