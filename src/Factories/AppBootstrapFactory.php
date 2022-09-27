<?php

declare(strict_types=1);

namespace Thomasderooij\LaravelModules\Factories;

use Illuminate\Contracts\Filesystem\FileNotFoundException;
use Illuminate\Filesystem\Filesystem;
use Thomasderooij\LaravelModules\Contracts\ConsoleCompositeKernel;
use Thomasderooij\LaravelModules\Contracts\Factories\AppBootstrapFactory as Contract;
use Thomasderooij\LaravelModules\Contracts\HttpCompositeKernel;
use Thomasderooij\LaravelModules\Contracts\Services\ModuleManager;

class AppBootstrapFactory extends FileFactory implements Contract
{
    protected string $compositeConsoleKernelClassName;
    protected string $compositeHttpKernelClassName;

    public function __construct(
        Filesystem $filesystem,
        ConsoleCompositeKernel $consoleCompositeKernel,
        HttpCompositeKernel $httpCompositeKernel,
        ModuleManager $moduleManager
    ) {
        parent::__construct($filesystem, $moduleManager);

        $this->compositeConsoleKernelClassName = get_class($consoleCompositeKernel);
        $this->compositeHttpKernelClassName = get_class($httpCompositeKernel);
    }

    /**
     * Rename the bootstrap file and replace it with a new one
     *
     * @throws FileNotFoundException
     */
    public function create(): void
    {
        $this->renameBootstrapFile();
        $this->writeNewBootstrapFile();
    }

    /**
     * Revert the bootstrap file to its original
     */
    public function undo(): void
    {
        $this->filesystem->delete(base_path("bootstrap/{$this->getBootstrapFileName()}"));
        $this->filesystem->move(
            base_path("bootstrap/{$this->getBootstrapOrigFileName()}"),
            base_path("bootstrap/{$this->getBootstrapFileName()}")
        );
    }

    /**
     * Create a new bootstrap file
     *
     * @throws FileNotFoundException
     */
    protected function writeNewBootstrapFile(): void
    {
        touch(base_path("bootstrap/{$this->getBootstrapFileName()}"));

        $this->populateFile(base_path("bootstrap"), $this->getBootstrapFileName(), $this->getStub(), [
            $this->getConsoleCompositeKernelPlaceHolder() => $this->getCompositeKernelClassNameStatic(),
            $this->getHttpCompositeKernelPlaceHolder() => $this->getHttpKernelClassNameStatic(),
        ]);
    }

    /**
     * Get the stub file location
     *
     * @return string
     */
    protected function getStub(): string
    {
        return __DIR__ . "/stubs/bootstrapFile.stub";
    }

    /**
     * Get the compositeKernel classname placeholder
     *
     * @return string
     */
    protected function getConsoleCompositeKernelPlaceHolder(): string
    {
        return "{ConsoleCompositeKernel}";
    }

    /**
     * get the compositeKernel static class function string
     *
     * @return string
     */
    protected function getCompositeKernelClassNameStatic(): string
    {
        return $this->compositeConsoleKernelClassName . "::class";
    }

    protected function getHttpCompositeKernelPlaceHolder(): string
    {
        return "{HttpCompositeKernel}";
    }

    public function getHttpKernelClassNameStatic(): string
    {
        return $this->compositeHttpKernelClassName . "::class";
    }

    /**
     * Rename the original bootstrap file
     */
    protected function renameBootstrapFile(): void
    {
        $this->filesystem->move(
            base_path("bootstrap/{$this->getBootstrapFileName()}"),
            base_path("bootstrap/{$this->getBootstrapOrigFileName()}")
        );
    }

    /**
     * Get the bootstrap file name
     *
     * @return string
     */
    protected function getBootstrapFileName(): string
    {
        return "app.php";
    }

    /**
     * Get the bootstrap file name where the original file wil be relocated
     *
     * @return string
     */
    protected function getBootstrapOrigFileName(): string
    {
        return "app_orig.php";
    }
}
