<?php

declare(strict_types=1);

namespace Thomasderooij\LaravelModules\Contracts\Services;

interface ComposerEditor
{
    public function hasNamespaceInAutoload(string $namespace): bool;

    /**
     * Add a namespace for your module root directory
     */
    public function addNamespaceToAutoload(string $rootDir): void;

    /**
     * Remove the namespace for your module root directory
     */
    public function removeNamespaceFromAutoload(string $rootDir = null): void;
}
