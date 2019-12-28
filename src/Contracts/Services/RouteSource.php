<?php

namespace Thomasderooij\LaravelModules\Contracts\Services;

interface RouteSource
{
    public function getWebRoute () : string;

    public function getApiRoute () : string;

    public function getConsoleRoute () : string;

    public function getRouteRootDir () : string;

    public function getRouteFileExtension () : string;

    public function getRouteFiles () : array;
}
