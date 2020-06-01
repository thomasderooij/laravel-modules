<?php

namespace Thomasderooij\LaravelModules\Factories;

use Illuminate\Contracts\Filesystem\FileNotFoundException;
use Thomasderooij\LaravelModules\Http\CompositeKernel;
use Thomasderooij\LaravelModules\Contracts\Factories\HttpKernelFactory as Contract;
use Thomasderooij\LaravelModules\Exceptions\InitExceptions\ConfigFileNotFoundException;

class HttpKernelFactory extends FileFactory implements Contract
{
    /**
     * @param string $module
     * @throws ConfigFileNotFoundException
     * @throws FileNotFoundException
     */
    public function create(string $module): void
    {
        $this->populateFile(base_path($this->getRelativeConsoleDir($module)), $this->getKernelFileName(), $this->getStub(), [
            $this->getKernelNamespacePlaceholder() => $this->getKernelNamespace($module),
            $this->getModuleKernelPlaceholder() => $this->getModuleKernel(),
        ]);
    }

    /**
     * Get the relative path to the console kernel
     *
     * @param string $module
     * @return string
     */
    protected function getRelativeConsoleDir (string $module) : string
    {
        return config("modules.root") . "/$module/{$this->getHttpDirectory()}";
    }

    /**
     * Get the file stub
     *
     * @return string
     */
    protected function getStub () : string
    {
        return __DIR__ . '/stubs/httpKernel.stub';
    }

    /**
     * Get the kernel namespace placeholder
     *
     * @return string
     */
    protected function getKernelNamespacePlaceholder () : string
    {
        return "{kernelNamespace}";
    }

    /**
     * Get the kernel namespace
     *
     * @param string $module
     * @return string
     * @throws ConfigFileNotFoundException
     */
    protected function getKernelNamespace (string $module) : string
    {
        return $this->moduleManager->getModuleNameSpace($module) . $this->getHttpDirectory();
    }

    /**
     * Get the module kernel classname placeholder
     *
     * @return string
     */
    protected function getModuleKernelPlaceholder ( ): string
    {
        return "{moduleKernel}";
    }

    /**
     * Get the module kernel class
     *
     * @return string
     */
    protected function getModuleKernel () : string
    {
        return CompositeKernel::class;
    }

    /**
     * Get the kernel directory within the module
     *
     * @return string
     */
    protected function getHttpDirectory () : string
    {
        return "Http";
    }

    /**
     * Get the kernel file name
     *
     * @return string
     */
    protected function getKernelFileName () : string
    {
        return "Kernel.php";
    }
}
