<?php

declare(strict_types=1);

namespace Thomasderooij\LaravelModules\Factories;

use Illuminate\Contracts\Filesystem\FileNotFoundException as FileNotFoundExceptionAlias;
use Thomasderooij\LaravelModules\Contracts\Factories\ControllerFactory as Contract;
use Thomasderooij\LaravelModules\Exceptions\InitExceptions\ConfigFileNotFoundException;
use Thomasderooij\LaravelModules\Exceptions\InitExceptions\ModulesNotInitialisedException;

class ControllerFactory extends FileFactory implements Contract
{
    /**
     * Create a base controller for a new module
     *
     * @param string $module
     * @throws FileNotFoundExceptionAlias
     * @throws ConfigFileNotFoundException
     */
    public function create (string $module) : void
    {
        $this->populateFile($this->getDir($module), $this->getFileName(), $this->getStub(), [
            $this->getNamespacePlaceholder() => $this->getNamespace($module),
            $this->getClassNamePlaceHolder() => $this->getClassName(),
        ]);
    }

    /**
     * Get the qualified classname of the base controller for a given module
     *
     * @param string $module
     * @return string
     * @throws ConfigFileNotFoundException
     */
    public function getQualifiedClassName (string $module) : string
    {
        return $this->getNamespace($module) . "\\" . $this->getClassName();
    }

    /**
     * Get the controller classname
     *
     * @return string
     */
    protected function getClassName () : string
    {
        return "Controller";
    }

    /**
     * Get the controller namespace
     *
     * @param string $module
     * @return string
     * @throws ConfigFileNotFoundException
     */
    protected function getNamespace (string $module) : string
    {
        return $this->moduleManager->getModuleNamespace($module) . "Http\\Controllers";
    }

    /**
     * Get the stub class placeholder
     *
     * @return string
     */
    protected function getClassNamePlaceHolder () : string
    {
        return "{class}";
    }

    /**
     * Get the stub namespace placeholder
     *
     * @return string
     */
    protected function getNamespacePlaceholder () : string
    {
        return "{namespace}";
    }

    /**
     * Get the directory in which to place the controller file
     *
     * @param string $module
     * @return string
     */
    protected function getDir (string $module) : string
    {
        return $this->moduleManager->getModuleDirectory($module) . "/Http/Controllers";
    }

    /**
     * Get the controller stub location
     *
     * @return string
     */
    protected function getStub () : string
    {
        return __DIR__ . "/stubs/controller.stub";
    }

    /**
     * Get the controller file name
     *
     * @return string
     */
    protected function getFileName () : string
    {
        return $this->getClassName() . ".php";
    }
}
