<?php

namespace Thomasderooij\LaravelModules\Factories;

use App\Providers\AuthServiceProvider;
use App\Providers\BroadcastServiceProvider;
use App\Providers\EventServiceProvider;
use App\Providers\RouteServiceProvider;
use Illuminate\Contracts\Filesystem\FileNotFoundException;
use Illuminate\Filesystem\Filesystem;
use Thomasderooij\LaravelModules\CompositeProviders\AuthCompositeServiceProvider;
use Thomasderooij\LaravelModules\CompositeProviders\BroadcastCompositeServiceProvider;
use Thomasderooij\LaravelModules\CompositeProviders\EventCompositeServiceProvider;
use Thomasderooij\LaravelModules\CompositeProviders\RouteCompositeServiceProvider;
use Thomasderooij\LaravelModules\Contracts\Factories\ConfigFactory as Contract;
use Thomasderooij\LaravelModules\Contracts\Services\ComposerEditor;
use Thomasderooij\LaravelModules\Contracts\Services\ModuleManager;

class ConfigFactory extends FileFactory implements Contract
{
    /**
     * A service to edit the composer file to match the module functionality
     *
     * @var ComposerEditor
     */
    protected $composerEditor;

    public function __construct (Filesystem $filesystem, ModuleManager $moduleManager, ComposerEditor $composerEditor)
    {
        parent::__construct($filesystem, $moduleManager);

        $this->moduleManager = $moduleManager;
        $this->composerEditor = $composerEditor;
    }

    /**
     * Create modules config files and metadata files
     *
     * @param string $rootDir
     * @throws FileNotFoundException
     */
    public function create (string $rootDir) : void
    {
        $this->createConfigFile($rootDir);
        $this->createModuleTrackerFile($rootDir);
        $this->replaceServiceProviders();
        $this->composerEditor->addNamespaceToAutoload($rootDir);
    }

    /**
     * Undo the creation of the config files and metadata
     *
     * @throws FileNotFoundException
     */
    public function undo () : void
    {
        $this->composerEditor->removeNamespaceFromAutoload();
        $this->removeTrackerFile();
        $this->removeConfigFile();
    }

    /**
     * Remove the module tracker file
     */
    protected function removeTrackerFile () : void
    {
        $dir = base_path($this->getModuleDirPlaceholder());
        $this->fileSystem->cleanDirectory($dir);
        rmdir($dir);
    }

    /**
     * Remove the modules config file
     */
    protected function removeConfigFile () : void
    {
        $this->fileSystem->delete(base_path('config') . "/{$this->getConfigFileName()}");
    }

    /**
     * Create a modules config file
     *
     * @param string $rootDir
     * @throws FileNotFoundException
     */
    protected function createConfigFile (string $rootDir) : void
    {
        $this->populateFile(base_path("config"), $this->getConfigFileName(), $this->getStub(), [
            $this->getModuleDirPlaceholder() => $rootDir,
            $this->getModuleNamespacePlaceholder() => ucfirst($rootDir),
            $this->getModuleAutoloadPlaceholder() => $rootDir,
            $this->getVanillaModuleNamePlaceholder() => $this->getDefaultVanillaModuleName(),
        ]);
    }

    /**
     * Create a module tracker file
     *
     * @throws FileNotFoundException
     */
    protected function createModuleTrackerFile (string $rootDir) : void
    {
        $this->populateFile($this->getRelativeStorageDirectory($rootDir), $this->moduleManager->getTrackerFileName(), $this->getTrackerStub());
    }

    /**
     * Replace the service providers in the config/app.php file with their composite counterparts.
     *
     * @throws FileNotFoundException
     */
    protected function replaceServiceProviders () : void
    {
        $this->populateFile(config_path(), "app.php", config_path("app.php"), [
            AuthServiceProvider::class => AuthCompositeServiceProvider::class,
            BroadcastServiceProvider::class => BroadcastCompositeServiceProvider::class,
            EventServiceProvider::class => EventCompositeServiceProvider::class,
            RouteServiceProvider::class => RouteCompositeServiceProvider::class,
        ]);
    }

    /**
     * Get the location of the file stub
     *
     * @return string
     */
    protected function getStub(): string
    {
        return __DIR__ . "/stubs/config.stub";
    }

    /**
     * Get the tracker stub file location
     *
     * @return string
     */
    protected function getTrackerStub () : string
    {
        return __DIR__ . '/stubs/tracker.stub';
    }

    /**
     * Get the module stub directory placeholder
     *
     * @return string
     */
    protected function getModuleDirPlaceholder () : string
    {
        return "{moduleDirectory}";
    }

    /**
     * Get the module stub namespace placeholder
     *
     * @return string
     */
    protected function getModuleNamespacePlaceholder () : string
    {
        return "{moduleDirectoryUcfirst}";
    }

    /**
     * Get the composer psr4 autoload placeholder
     *
     * @return string
     */
    protected function getModuleAutoloadPlaceholder () : string
    {
        return "{moduleAutoload}";
    }

    /**
     * Get the modules config filename
     *
     * @return string
     */
    public function getConfigFileName () : string
    {
        return "modules.php";
    }

    /**
     * Get the directory in which module metadata gets stored
     *
     * @return string
     */
    public function getRelativeStorageDirectory (string $rootDir) : string
    {
        return $rootDir;
    }

    /**
     * Get the vanilla module name placeholder
     *
     * @return string
     */
    public function getVanillaModuleNamePlaceholder () : string
    {
        return "{vanillaModule}";
    }

    /**
     * Get the default value for the vanilla module
     *
     * @return string
     */
    public function getDefaultVanillaModuleName () : string
    {
        return "Vanilla";
    }
}
