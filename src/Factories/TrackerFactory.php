<?php

declare(strict_types=1);

namespace Thomasderooij\LaravelModules\Factories;

use Illuminate\Contracts\Filesystem\FileNotFoundException;
use Thomasderooij\LaravelModules\Contracts\Factories\TrackerFactory as Contract;

class TrackerFactory extends FileFactory implements Contract
{
    /**
     * Create a tracker file in the modules directory
     *
     * @param string $rootDir
     * @throws FileNotFoundException
     */
    public function create(string $rootDir): void
    {
        $this->populateFile($rootDir, $this->moduleManager->getTrackerFileName(), $this->getTrackerStub());
    }

    /**
     * Get the tracker stub file location
     *
     * @return string
     */
    protected function getTrackerStub () : string
    {
        return __DIR__ . '/stubs/tracker.stub';
    }
}
