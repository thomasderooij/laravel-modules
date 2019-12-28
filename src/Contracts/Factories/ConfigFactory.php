<?php

namespace Thomasderooij\LaravelModules\Contracts\Factories;

interface ConfigFactory
{
    public function create (string $rootDir) : void;

    public function undo () : void;
}
