<?php

declare(strict_types=1);

namespace Thomasderooij\LaravelModules\Factories;

use Illuminate\Contracts\Filesystem\FileNotFoundException;
use Thomasderooij\LaravelModules\Http\CompositeKernel;
use Thomasderooij\LaravelModules\Contracts\Factories\HttpKernelFactory as Contract;

class HttpKernelFactory extends FileFactory implements Contract
{
    /**
     * @param string $module
     * @throws FileNotFoundException
     */
    public function create(string $module): void
    {
        $this->populateFile($this->getHttpDir($module), $this->getKernelFileName(), $this->getStub(), [
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
    protected function getHttpDir(string $module): string
    {
        return base_path(config("modules.root")) . "/$module/{$this->getHttpDirectory()}";
    }

    /**
     * Get the file stub
     *
     * @return string
     */
    protected function getStub(): string
    {
        return __DIR__ . '/stubs/httpKernel.stub';
    }

    /**
     * Get the kernel namespace placeholder
     *
     * @return string
     */
    protected function getKernelNamespacePlaceholder(): string
    {
        return "{kernelNamespace}";
    }

    /**
     * Get the kernel namespace
     *
     * @param string $module
     * @return string
     */
    protected function getKernelNamespace(string $module): string
    {
        return $this->moduleManager->getModuleNamespace($module) . $this->getHttpDirectory();
    }

    /**
     * Get the module kernel classname placeholder
     *
     * @return string
     */
    protected function getModuleKernelPlaceholder(): string
    {
        return "{moduleKernel}";
    }

    /**
     * Get the module kernel class
     *
     * @return string
     */
    protected function getModuleKernel(): string
    {
        return CompositeKernel::class;
    }

    /**
     * Get the kernel directory within the module
     *
     * @return string
     */
    protected function getHttpDirectory(): string
    {
        return "Http";
    }

    /**
     * Get the kernel file name
     *
     * @return string
     */
    protected function getKernelFileName(): string
    {
        return "Kernel.php";
    }
}
