<?php

namespace Thomasderooij\LaravelModules\Contracts\Services;

interface RouteSource
{
    /**
     * Get the web route file
     *
     * @return string
     */
    public function getWebRoute () : string;

    /**
     * Get the api route file
     *
     * @return string
     */
    public function getApiRoute () : string;

    /**
     * Get the console route file
     *
     * @return string
     */
    public function getConsoleRoute () : string;

    /**
     * Get the console channels file
     *
     * @return string
     */
    public function getChannelsRoute () : string;

    /**
     * Get the route root dir
     *
     * @return string
     */
    public function getRouteRootDir () : string;

    /**
     * Get the route file extension
     *
     * @return string
     */
    public function getRouteFileExtension () : string;

    /**
     * Get the standard route files
     *
     * @return array
     */
    public function getRouteFiles () : array;
}
