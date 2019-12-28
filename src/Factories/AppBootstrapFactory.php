<?php

namespace Thomasderooij\LaravelModules\Factories;

use Illuminate\Contracts\Filesystem\FileNotFoundException;
use Illuminate\Filesystem\Filesystem;
use Thomasderooij\LaravelModules\Contracts\ConsoleCompositeKernel;
use Thomasderooij\LaravelModules\Contracts\Factories\AppBootstrapFactory as Contract;
use Thomasderooij\LaravelModules\Contracts\Services\ModuleManager;

class AppBootstrapFactory extends FileFactory implements Contract
{
    protected $compositeConsoleKernelClassName;

    public function __construct(Filesystem $filesystem, ConsoleCompositeKernel $compositeKernel, ModuleManager $moduleManager)
    {
        parent::__construct($filesystem, $moduleManager);

        $this->compositeConsoleKernelClassName = get_class($compositeKernel);
    }

    /**
     * Rename the bootstrap file and replace it with a new one
     *
     * @throws FileNotFoundException
     */
    public function create () : void
    {
        $this->renameBootstrapFile();
        $this->writeNewBootstrapFile();
    }

    /**
     * Revert the bootstrap file to its original
     */
    public function undo () : void
    {
        $this->fileSystem->delete(base_path("bootstrap/{$this->getBootstrapFileName()}"));
        $this->fileSystem->move(base_path("bootstrap/{$this->getBootstrapOrigFileName()}"), base_path("bootstrap/{$this->getBootstrapFileName()}"));
    }

    /**
     * Create a new bootstrap file
     *
     * @throws FileNotFoundException
     */
    protected function writeNewBootstrapFile () : void
    {
        touch(base_path("bootstrap/{$this->getBootstrapFileName()}"));

        $this->populateFile(base_path("bootstrap"), $this->getBootstrapFileName(), $this->getStub(), [
            $this->getConsoleCompositeKernelPlaceHolder() => $this->getCompositeKernelClassNameStatic()
        ]);
    }

    /**
     * Get the stub file location
     *
     * @return string
     */
    protected function getStub () : string
    {
        return base_path("bootstrap/{$this->getBootstrapOrigFileName()}");
    }

    /**
     * Get the compositeKernel classname placeholder
     *
     * @return string
     */
    protected function getConsoleCompositeKernelPlaceHolder () : string
    {
        return "App\Console\Kernel::class";
    }

    /**
     * get the compositeKernel static class function string
     *
     * @return string
     */
    protected function getCompositeKernelClassNameStatic () : string
    {
        return $this->compositeConsoleKernelClassName . "::class";
    }

    /**
     * Rename the original bootstrap file
     */
    protected function renameBootstrapFile () : void
    {
        $this->fileSystem->move(base_path("bootstrap/{$this->getBootstrapFileName()}"), base_path("bootstrap/{$this->getBootstrapOrigFileName()}"));
    }

    /**
     * Get the bootstrap file name
     *
     * @return string
     */
    protected function getBootstrapFileName () : string
    {
        return "app.php";
    }

    /**
     * Get the bootstrap file name where the original file wil be relocated
     *
     * @return string
     */
    protected function getBootstrapOrigFileName () : string
    {
        return "app_orig.php";
    }
}
