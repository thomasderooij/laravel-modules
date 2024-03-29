<?php

declare(strict_types=1);

namespace Thomasderooij\LaravelModules\Services;

use Thomasderooij\LaravelModules\Contracts\Services\DependencyHandler as Contract;
use Thomasderooij\LaravelModules\Exceptions\DependencyExceptions\CircularReferenceException;
use Thomasderooij\LaravelModules\Exceptions\DependencyExceptions\DependencyAlreadyExistsException;
use Thomasderooij\LaravelModules\Exceptions\DependencyExceptions\DependencyNotFoundException;
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

        // Make sure there is not confusion about module names
        $downstream = $this->sanitiseModuleName($downstream);
        $upstream = $this->sanitiseModuleName($upstream);

        // Throw an exception if the dependency already exists
        if ($this->dependencyExists($downstream, $upstream)) {
            throw new DependencyAlreadyExistsException(
                $downstream,
                $upstream,
                "module \"{$downstream}\" is already dependent on \"{$upstream}\"."
            );
        }

        if ($this->wouldCreateCircularReference($downstream, $upstream)) {
            throw new CircularReferenceException(
                $downstream,
                $upstream,
                "module \"{$upstream}\" is already upstream of \"{$downstream}\"."
            );
        }

        // Get that sweet, sweet content
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
        $fileContent = $this->getTrackerContent();
        $modules = $this->getModules();

        // Return all modules except this one if the dependencies key is not set
        if (!isset($fileContent[$this->getDependenciesKey()])) {
            return array_values(array_diff($modules, [$module]));
        }

        $dependencies = $fileContent[$this->getDependenciesKey()];
        // Filter the dependencies in which the module is downstream
        $filtered = array_filter($dependencies, function (array $dependency) use ($module) {
            return $dependency[("down")] === $module;
        });

        // Then we map everything that is upstream
        $directUpstream = array_values(
            array_map(function (array $dependency) {
                return $dependency["up"];
            }, $filtered)
        );
        $downstream = $this->getDownstreamModules($module, $dependencies);

        // We remove direct upstream modules from the list
        $modules = array_diff($modules, $directUpstream);
        // We remove downstream modules from the list
        $modules = array_diff($modules, $downstream);
        // And we remove this module from the list
        $modules = array_diff($modules, [$module]);

        return array_values($modules);
    }

    public function getModulesInMigrationOrder(): array
    {
        // We get the tracker content, and the active modules
        $trackerContent = $this->getTrackerContent();
        $modules = $this->getActiveModules();

        // If there are no dependencies, all modules act as independent pieces of software, and their migrations
        //  should not refer to one another
        $vanilla = config("modules.vanilla");
        if (!isset($trackerContent[$this->getDependenciesKey()])) {
            return array_merge([$vanilla], $modules);
        }

        // If there are dependencies, we separate them into layers, starting with ones that have no dependencies
        //  Next layer are the ones whose only dependencies are the ones in the first layer
        //  Third is modules whose only dependencies are in the 2nd layer, etc
        $dependencies = $trackerContent[$this->getDependenciesKey()];
        $list = [$vanilla];
        $migratables = null;
        while ($migratables !== []) {
            $migratables = $this->getModulesMigratableAfterList($list, $dependencies);
            $list = array_merge($list, $migratables);
        }

        // We check which modules are not in the dependencies, and add those to the already sorted dependencies
        $unrelatedModules = array_diff($modules, $list);

        // And then we return all of the modules, in migratable order
        return array_merge($list, $unrelatedModules);
    }

    protected function getModulesMigratableAfterList(array $list, array $dependencies): array
    {
        $upReferences = [];
        $downReferences = [];
        foreach ($dependencies as $dependency) {
            // If the up reference is found in the previous tier, it's allowed to be downstream
            if (array_search($dependency["up"], $list) !== false) {
                $upReferences[] = $dependency["down"];
                continue;
            }
            // Otherwise, we just add the references to thier respective arrays
            $upReferences[] = $dependency["up"];
            $downReferences[] = $dependency["down"];
        }

        // Next we return the upstream references which are not found the downstream references or in the previous tier
        return array_values(array_diff(array_unique($upReferences), array_unique($downReferences), $list));
    }

    public function removeDependency(string $downstream, string $upstream): void
    {
        // Throw an exception if a module does not exist
        if (!$this->hasModule($downstream)) {
            throw new ModuleNotFoundException("There is no module named \"$downstream\".");
        }
        if (!$this->hasModule($upstream)) {
            throw new ModuleNotFoundException("There is no module named \"$upstream\".");
        }

        // Make sure there is no confusion about module names
        $downstream = $this->sanitiseModuleName($downstream);
        $upstream = $this->sanitiseModuleName($upstream);

        // Get the tracker content first
        $fileContent = $this->getTrackerContent();

        // If there is no dependencies key, there are no dependencies
        if (!$this->dependencyExists($downstream, $upstream)) {
            throw new DependencyNotFoundException($downstream, $upstream, "No such dependency exists.");
        }

        // Fish out our dependency
        $dependenciesKey = $this->getDependenciesKey();
        $dependencies = $fileContent[$dependenciesKey];
        $filtered = array_filter($dependencies, function (array $dependency) use ($downstream, $upstream) {
            return $dependency["up"] === $upstream && $dependency["down"] === $downstream;
        });

        // If you can't find it, throw an exception
        if (empty($filtered)) {
            throw new DependencyNotFoundException($downstream, $upstream, "No such dependency exists.");
        }

        $newContent = array_filter($dependencies, function (array $dependency) use ($upstream, $downstream) {
            return $dependency["up"] !== $upstream && $dependency["down"] !== $downstream;
        });

        // Add it to the tracker file
        $fileContent[$dependenciesKey] = $newContent;
        // And save it
        $this->save($fileContent);
    }

    protected function dependencyExists(string $downstream, string $upstream): bool
    {
        $fileContent = $this->getTrackerContent();

        // Return false if the dependencies key is not set
        if (!isset($fileContent[$this->getDependenciesKey()])) {
            return false;
        }

        $result = array_search(["up" => $upstream, "down" => $downstream], $fileContent[$this->getDependenciesKey()]);

        return $result !== false;
    }

    protected function getDependenciesKey(): string
    {
        return "dependencies";
    }

    /**
     * @param string $downstream
     * @param string $upstream
     * @return bool
     */
    protected function wouldCreateCircularReference(string $downstream, string $upstream): bool
    {
        // A module can not be its down dependency
        if ($downstream === $upstream) {
            return true;
        }

        // get the tracker content
        $fileContent = $this->getTrackerContent();

        // Return false if the dependencies key is not set
        if (!isset($fileContent[$this->getDependenciesKey()])) {
            return false;
        }

        // single out the dependencies
        $dependencies = $fileContent[$this->getDependenciesKey()];
        // See what's upstream
        $downstreamModules = $this->getDownstreamModules($downstream, $dependencies);

        // And make sure that whatever we're adding is not already downstream somewhere
        return array_search($upstream, $downstreamModules) !== false;
    }

    /**
     * Fetch all the modules upstream of the one provided
     *
     * @param string $module
     * @param array|null $dependencies
     * @return array
     */
    public function getUpstreamModules(string $module, array $dependencies = null): array
    {
        return $this->getModulesFromStream($module, $dependencies, true);
    }

    /**
     * Fetch all the modules downstream of the one provided
     *
     * @param string $module
     * @param array|null $dependencies
     * @return array
     */
    public function getDownstreamModules(string $module, array $dependencies = null): array
    {
        return $this->getModulesFromStream($module, $dependencies, false);
    }

    protected function getModulesFromStream(string $module, array $dependencies = null, bool $up = true): array
    {
        if ($dependencies === null) {
            // get the tracker content
            $fileContent = $this->getTrackerContent();

            // single out the dependencies
            $dependencies = $fileContent[$this->getDependenciesKey()] ?? [];
        }

        // Filter the dependencies in which the module is downstream
        $filtered = array_filter($dependencies, function (array $dependency) use ($module, $up) {
            return $dependency[($up ? "down" : "up")] === $module;
        });

        // Then we map everything that is upstream
        $mapped = array_values(
            array_map(function (array $dependency) use ($up) {
                return $dependency[($up ? "up" : "down")];
            }, $filtered)
        );

        // Foreach of these, we take the module that is upstream
        foreach ($mapped as $dependency) {
            $furtherUp = $this->getModulesFromStream($dependency, $dependencies, $up);
            // this is pretty key, since array_merge passes a reference, and we don't want that. And I thought I was going crazy for a bit.
            $clone = $furtherUp;
            $mapped = array_merge($mapped, $clone);
        }

        return array_unique($mapped);
    }

    public function removeDependencies(string $module): void
    {
        if (!$this->hasTrackerFile()) {
            return;
        }

        $trackerContent = $this->getTrackerContent();
        if (!key_exists($this->getDependenciesKey(), $trackerContent)) {
            return;
        }

        $dependencies = $trackerContent[$this->getDependenciesKey()];
        foreach ($dependencies as $key => $dependency) {
            if (strtolower($dependency["up"]) === strtolower($module) || strtolower($dependency["down"]) === strtolower(
                    $module
                )) {
                unset($dependencies[$key]);
            }
        }

        $trackerContent[$this->getDependenciesKey()] = $dependencies;
        $this->save($trackerContent);
    }
}
