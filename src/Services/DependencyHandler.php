<?php

declare(strict_types=1);

namespace Thomasderooij\LaravelModules\Services;

use Thomasderooij\LaravelModules\Contracts\Services\DependencyHandler as Contract;

class DependencyHandler extends ModuleStateRepository implements Contract
{
    public function addDependency(string $downstream, string $upstream): void
    {
        $fileContent = $this->getTrackerContent();

        if (!isset($fileContent[$key = $this->getDependenciesKey()])) {
            $fileContent[$key] = [];
        }

        $fileContent[$key][] = ["up" => $upstream, "down" => $downstream];

        $this->save($fileContent);
    }

    public function getAvailableModules(string $module): array
    {
        // TODO: Implement getAvailableModules() method.
    }

    private function getDependenciesKey () : string
    {
        return "dependencies";
    }
}
