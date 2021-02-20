<?php

declare(strict_types=1);

namespace Thomasderooij\LaravelModules\Services;

use Thomasderooij\LaravelModules\Contracts\Services\DependencyHandler as Contract;
use Thomasderooij\LaravelModules\Exceptions\DependencyExceptions\CircularReferenceException;
use Thomasderooij\LaravelModules\Exceptions\DependencyExceptions\DependencyAlreadyExistsException;
use Thomasderooij\LaravelModules\Exceptions\ModuleNotFoundException;

class DependencyHandler extends ModuleStateRepository implements Contract
{
    public function addDependency(string $downstream, string $upstream): void
    {
        // Throw an exception if a module does not exist
        if (!$this->hasModule($downstream)) {
            throw new ModuleNotFoundException("There is no module named \"$downstream\".");
        }
        if (!$this->hasModule($upstream)) {
            throw new ModuleNotFoundException("There is no module named \"$upstream\".");
        }

        $downstream = $this->sanitiseModuleName($downstream);
        $upstream = $this->sanitiseModuleName($upstream);

        // Throw an exception if the dependency already exists
        if ($this->dependencyExists($downstream, $upstream)) {
            throw new DependencyAlreadyExistsException($downstream, $upstream, "module \"{$downstream}\" is already dependent on \"{$upstream}\".");
        }

        $fileContent = $this->getTrackerContent();

        if ($this->wouldCreateCircularReference($downstream, $upstream)) {
            throw new CircularReferenceException($downstream, $upstream, "module \"{$upstream}\" is already upstream of \"{$downstream}\".");
        }

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

    protected function dependencyExists(string $downstream, string $upstream) : bool
    {
        $fileContent = $this->getTrackerContent();

        // Return false if the dependencies key is not set
        if (!isset($fileContent[$this->getDependenciesKey()])) { return false; }

        $result = array_search(["up" => $upstream, "down" => $downstream], $fileContent[$this->getDependenciesKey()]);

        return $result !== false;
    }

    protected function getDependenciesKey () : string
    {
        return "dependencies";
    }

    protected function wouldCreateCircularReference (string $downstream, string $upstream) : bool
    {
        // A module can not be its down dependency
        if (strtolower($downstream) === strtolower($upstream)) {
            return true;
        }

        $fileContent = $this->getTrackerContent();

        // Return false if the dependencies key is not set
        if (!isset($fileContent[$this->getDependenciesKey()])) { return false; }

        $dependencies = $fileContent[$this->getDependenciesKey()];
    }

    protected function getUpstreamModules (string $module, array $dependencies) : array
    {
        // Filter the dependencies in which the module is downstream
        $filtered = array_filter($dependencies, function (array $dependency) use ($module) {
            return $dependency["down"] === $module;
        });

        // Then we map everything that is upstream
        $mapped = array_values(array_map(function (array $dependency) {
            return $dependency["up"];
        }, $filtered));

        // Foreach of these, we take the module that is upstream
        foreach ($mapped as $dependency) {
            $furtherUp = $this->getUpstreamModules($dependency, $dependencies);
            // this is pretty key, since array_merge passes a reference, and we don't want that. And I thought I was going crazy for a bit.
            $clone = $furtherUp;
            $mapped = array_merge($mapped, $clone);
        }

        return array_unique($mapped);
    }
}
