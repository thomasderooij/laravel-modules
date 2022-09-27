<?php

declare(strict_types=1);

namespace Thomasderooij\LaravelModules\Factories;

use Illuminate\Contracts\Filesystem\FileNotFoundException;
use Illuminate\Filesystem\Filesystem;
use Thomasderooij\LaravelModules\Contracts\Services\ModuleManager;

abstract class FileFactory
{
    protected Filesystem $filesystem;
    protected ModuleManager $moduleManager;

    public function __construct(Filesystem $filesystem, ModuleManager $moduleManager)
    {
        $this->filesystem = $filesystem;
        $this->moduleManager = $moduleManager;
    }

    /**
     * Populate a stub file, and place it in the specified directory
     *
     * @param string $dir
     * @param string $fileName
     * @param string $stub
     * @param array $values
     * @throws FileNotFoundException
     */
    protected function populateFile(string $dir, string $fileName, string $stub, array $values = []): void
    {
        $content = $this->filesystem->get($stub);

        foreach ($values as $key => $value) {
            $content = str_replace($key, $value, $content);
        }

        if (!is_dir($dir)) {
            $this->filesystem->makeDirectory($dir, 0755, true);
        }

        $this->filesystem->put($this->ensureSlash($dir) . $fileName, $content);
    }

    /**
     * Check if the directory ends with a slash. If not, add it.
     *
     * @param string $directory
     * @return string
     */
    protected function ensureSlash(string $directory): string
    {
        if (!str_ends_with($directory, "/")) {
            $directory .= "/";
        }

        return $directory;
    }
}
