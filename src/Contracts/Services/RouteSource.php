<?php

declare(strict_types=1);

namespace Thomasderooij\LaravelModules\Contracts\Services;

interface RouteSource
{
    /**
     * Get the web route file
     */
    public function getWebRoute () : string;

    /**
     * Get the api route file
     */
    public function getApiRoute () : string;

    /**
     * Get the console route file
     */
    public function getConsoleRoute () : string;

    /**
     * Get the console channels file
     */
    public function getChannelsRoute () : string;

    /**
     * Get the route root dir
     */
    public function getRouteRootDir () : string;

    /**
     * Get the route file extension
     */
    public function getRouteFileExtension () : string;

    /**
     * Get the standard route files
     */
    public function getRouteFiles () : array;
}
