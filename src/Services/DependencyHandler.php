<?php

declare(strict_types=1);

namespace Thomasderooij\LaravelModules\Services;

use Thomasderooij\LaravelModules\Contracts\Services\DependencyHandler as Contract;

class DependencyHandler extends ModuleStateRepository implements Contract
{
    public function addDependency(string $downstream, string $upstream): void
    {
        // TODO: Implement addDependency() method.
    }

    public function getAvailableModules(string $module): array
    {
        // TODO: Implement getAvailableModules() method.
    }
}
