<?php

declare(strict_types=1);

namespace Thomasderooij\LaravelModules\Services;

use Illuminate\Contracts\Filesystem\FileNotFoundException;
use Illuminate\Filesystem\Filesystem;
use Thomasderooij\LaravelModules\Contracts\Services\ComposerEditor as Contract;

class ComposerEditor implements Contract
{
    /**
     * The laravel file system
     *
     * @var Filesystem
     */
    protected $filesystem;

    public function __construct (Filesystem $filesystem)
    {
        $this->filesystem = $filesystem;
    }

    /**
     * @param string $namespace
     * @return bool
     * @throws FileNotFoundException
     */
    public function hasNamespaceInAutoload (string $namespace) : bool
    {
        $data = $this->getComposerData();

        return array_key_exists($this->getPsr4Key($namespace), $data["autoload"]["psr-4"]);
    }

    /**
     * Add a namespace for your module root directory
     *
     * @param string $rootDir
     * @throws FileNotFoundException
     */
    public function addNamespaceToAutoload (string $rootDir) : void
    {
        $json = $this->getUpdatedFileContent($rootDir, "add");
        $this->filesystem->put(base_path("composer.json"), $json);
    }

    /**
     * Remove the namespace for your module root directory
     *
     * @param null|string $rootDir
     * @throws FileNotFoundException
     */
    public function removeNamespaceFromAutoload (string $rootDir = null) : void
    {
        if ($rootDir === null) {
            $rootDir = config("modules.autoload");
        }

        if ($rootDir) {
            $json = $this->getUpdatedFileContent($rootDir, "remove");
            $this->filesystem->put(base_path("composer.json"), $json);
        }
    }

    /**
     * Get the edited content for your composer file
     *
     * @param $rootDir
     * @param string $action
     * @return string
     * @throws FileNotFoundException
     */
    protected function getUpdatedFileContent ($rootDir, string $action) : string
    {
        $data = $this->getComposerData();
        if ($action === "add") {
            $data["autoload"]["psr-4"][$this->getPsr4Key($rootDir)] = $this->getPsr4Value($rootDir);
        } else {
            unset($data["autoload"]["psr-4"][$this->getPsr4Key($rootDir)]);
        }


        return json_encode($data, $this->getJsonOptions());
    }

    /**
     * Get the content of your composer file in array form
     *
     * @return array
     * @throws FileNotFoundException
     */
    protected function getComposerData () : array
    {
        $fileContent = $this->filesystem->get(base_path("composer.json"));

        return json_decode($fileContent, true);
    }

    /**
     * Get your composer psr4 key
     *
     * @param string $rootDir
     * @return string
     */
    protected function getPsr4Key (string $rootDir) : string
    {
        return ucfirst($rootDir). "\\";
    }

    /**
     * Get your composer psr4 value
     *
     * @param string $rootDir
     * @return string
     */
    protected function getPsr4Value (string $rootDir) : string
    {
        return $rootDir."/";
    }

    /**
     * Get the sum of your json writing options
     *
     * @return int
     */
    protected function getJsonOptions () : int
    {
        $options = [
            JSON_PRETTY_PRINT,
            JSON_UNESCAPED_SLASHES,
        ];

        return array_sum($options);
    }
}
