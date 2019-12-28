<?php

namespace Thomasderooij\LaravelModules\CompositeProviders;

use Thomasderooij\LaravelModules\Exceptions\InitExceptions\ConfigFileNotFoundException;
use Thomasderooij\LaravelModules\Exceptions\InitExceptions\ModulesNotInitialisedException;
use Thomasderooij\LaravelModules\Exceptions\InitExceptions\TrackerFileNotFoundException;
use Thomasderooij\LaravelModules\Services\ModuleManager;

trait CompositeProviderTrait
{
    protected $providers = [];

    /**
     * CompositeProviderTrait constructor.
     *
     * @param $app
     * @throws ConfigFileNotFoundException
     * @throws ModulesNotInitialisedException
     * @throws TrackerFileNotFoundException
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
        foreach (ModuleManager::getActiveModules(true) as $module) {
            // Check if the module has the expected provider. If so, add it to the list of providers
            if (class_exists($this->getProvider($module, $this->name))) {
                $providers[] = $this->getProvider($module, $this->name);
            }
        }

        // Set the providers to the class providers property
        $this->providers = $providers;

        // Continue business as per usual
        parent::__construct($app);
    }

    /**
     * Get a provider qualified classname
     *
     * @param string $module
     * @param string $name
     * @return string
     * @throws ConfigFileNotFoundException
     */
    protected function getProvider (string $module, string $name) : string
    {
        return ModuleManager::getModuleNamespace($module) . "Providers\\{$name}";
    }
}
