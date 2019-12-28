<?php

namespace Thomasderooij\LaravelModules\Services;

use \Thomasderooij\LaravelModules\Contracts\Services\RouteSource as Contract;

class RouteSource implements Contract
{
    /**
     * Get the web route file
     *
     * @return string
     */
    public function getWebRoute () : string
    {
        return "web";
    }

    /**
     * Get the api route file
     *
     * @return string
     */
    public function getApiRoute () : string
    {
        return "api";
    }

    /**
     * Get the console route file
     *
     * @return string
     */
    public function getConsoleRoute () : string
    {
        return "console";
    }

    /**
     * Get the console channels file
     *
     * @return string
     */
    public function getChannelsRoute () : string
    {
        return "channels";
    }

    /**
     * Get the route root dir
     *
     * @return string
     */
    public function getRouteRootDir () : string
    {
        return "routes/";
    }

    /**
     * Get the route file extension
     *
     * @return string
     */
    public function getRouteFileExtension () : string
    {
        return ".php";
    }

    /**
     * Get the standard route files
     *
     * @return array
     */
    public function getRouteFiles () : array
    {
        return [
            $this->getWebRoute(),
            $this->getApiRoute(),
            $this->getConsoleRoute(),
        ];
    }
}
