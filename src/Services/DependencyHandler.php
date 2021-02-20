<?php

declare(strict_types=1);

namespace Thomasderooij\LaravelModules\Services;

use Thomasderooij\LaravelModules\Contracts\Services\DependencyHandler as Contract;
use Thomasderooij\LaravelModules\Exceptions\DependencyExceptions\DependencyAlreadyExistsException;

class DependencyHandler extends ModuleStateRepository implements Contract
{
    public function addDependency(string $downstream, string $upstream): void
    {
        // Throw an exception if the dependency already exists
        if ($this->dependencyExists($downstream, $upstream)) {
            throw new DependencyAlreadyExistsException($downstream, $upstream, "module \"{$downstream}\" is already dependent on \"{$upstream}\".");
        }

        $fileContent = $this->getTrackerContent();

        // Set the dependencies key if it does not already exist
        if (!isset($fileContent[$key = $this->getDependenciesKey()])) {
            $fileContent[$key] = [];
        }

        // Add the dependency, and save it to file
        $fileContent[$key][] = ["up" => $upstream, "down" => $downstream];
        $this->save($fileContent);
    }

    public function getAvailableModules(string $module): array
    {
        // TODO: Implement getAvailableModules() method.
    }

    private function dependencyExists(string $downstream, string $upstream) : bool
    {
        $fileContent = $this->getTrackerContent();

        // Return false if the dependencies key is not set
        if (!isset($fileContent[$this->getDependenciesKey()])) { return false; }

        $result = array_search(["up" => $upstream, "down" => $downstream], $fileContent[$this->getDependenciesKey()]);

        return $result !== false;
    }

    private function getDependenciesKey () : string
    {
        return "dependencies";
    }
}
