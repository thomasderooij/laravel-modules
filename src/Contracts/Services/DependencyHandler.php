<?php

declare(strict_types=1);

namespace Thomasderooij\LaravelModules\Contracts\Services;

interface DependencyHandler
{
    public function getAvailableModules (string $module) : array;

    public function addDependency (string $downstream, string $upstream) : void;
}
