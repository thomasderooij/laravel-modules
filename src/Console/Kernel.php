<?php

declare(strict_types=1);

namespace Thomasderooij\LaravelModules\Console;

use Illuminate\Console\Application as Artisan;
use Illuminate\Console\Command;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use ReflectionException;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Finder\SplFileInfo;

class Kernel extends ConsoleKernel
{
    /**
     * Register all of the commands in the given directories.
     *
     * @param array|string $paths
     * @return void
     * @throws ReflectionException
     */
    protected function load($paths) : void
    {
        $paths = array_unique(Arr::wrap($paths));

        $paths = array_filter($paths, function ($path) {
            return is_dir($path);
        });

        if (empty($paths)) {
            return;
        }

        $namespace = $this->app->getNamespace();
        /** @var SplFileInfo $command */
        foreach ((new Finder)->in($paths)->files() as $command) {

            // Determine if the command needs to be added from the module namespace or the vanilla namespace
            if (substr($command, 0, strlen(base_path(config('modules.root')))) === base_path(config('modules.root'))) {
                $command = ucfirst(config('modules.root')) . "\\".str_replace(
                        ['/', '.php'],
                        ['\\', ''],
                        Str::after($command->getPathname(), realpath(base_path(config('modules.root'))).DIRECTORY_SEPARATOR)
                    );
            } else {
                $command = $namespace.str_replace(
                        ['/', '.php'],
                        ['\\', ''],
                        Str::after($command->getPathname(), realpath(app_path()).DIRECTORY_SEPARATOR)
                    );
            }

            if (is_subclass_of($command, Command::class) &&
                ! (new \ReflectionClass($command))->isAbstract()) {
                Artisan::starting(function ($artisan) use ($command) {
                    $artisan->resolve($command);
                });
            }
        }
    }
}
