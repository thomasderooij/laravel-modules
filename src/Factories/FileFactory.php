<?php

namespace Thomasderooij\LaravelModules\Factories;

use Illuminate\Filesystem\Filesystem;
use Thomasderooij\LaravelModules\Contracts\Services\ModuleManager;

abstract class FileFactory
{
    /**
     * @var Filesystem $fileSystem
     */
    protected $fileSystem;

    /**
     * @var ModuleManager $moduleManager
     */
    protected $moduleManager;

    public function __construct (Filesystem $filesystem, ModuleManager $moduleManager)
    {
        $this->fileSystem = $filesystem;
        $this->moduleManager = $moduleManager;
    }

    /**
     * Populate a stub file, and place it in the specified directory
     *
     * @param string $dir
     * @param string $fileName
     * @param string $stub
     * @param array $values
     * @throws \Illuminate\Contracts\Filesystem\FileNotFoundException
     */
    protected function populateFile (string $dir, string $fileName, string $stub, array $values = []) : void
    {
        $content = $this->fileSystem->get($stub);

        foreach ($values as $key => $value) {
            $content = str_replace($key, $value, $content);
        }

        if (!is_dir($dir)) {
            mkdir($dir, 0777, true);
        }

        $this->fileSystem->put($this->ensureSlash($dir). $fileName, $content);
    }

    /**
     * Check if the directory ends with a slash. If not, add it.
     *
     * @param string $directory
     * @return string
     */
    protected function ensureSlash (string $directory) : string
    {
        if (substr($directory, -1, 1) !== "/") {
            $directory.= "/";
        }

        return $directory;
    }
}
