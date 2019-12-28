<?php

namespace Thomasderooij\LaravelModules\Contracts\Services;

use Illuminate\Contracts\Filesystem\FileNotFoundException;

interface ComposerEditor
{
    /**
     * @param string $namespace
     * @return bool
     * @throws FileNotFoundException
     */
    public function hasNamespaceInAutoload (string $namespace) : bool;

    /**
     * Add a namespace for your module root directory
     *
     * @param string $rootDir
     * @throws FileNotFoundException
     */
    public function addNamespaceToAutoload (string $rootDir) : void;

    /**
     * Remove the namespace for your module root directory
     *
     * @param null|string $rootDir
     * @throws FileNotFoundException
     */
    public function removeNamespaceFromAutoload (string $rootDir = null) : void;
}
