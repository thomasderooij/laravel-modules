<?php

declare(strict_types=1);

namespace Thomasderooij\LaravelModules\Tests\Services;

use Thomasderooij\LaravelModules\Services\RouteSource;
use Thomasderooij\LaravelModules\Tests\Test;

class RouteSourceTest extends Test
{
    public function testGetWebRoute () : void
    {
        $uut = $this->app->make("module.service.route_source");
        $this->assertSame("web", $uut->getWebRoute());
    }

    public function testGetApiRoute () : void
    {
        $uut = $this->app->make("module.service.route_source");
        $this->assertSame("api", $uut->getApiRoute());
    }

    public function testGetConsoleRoute () : void
    {
        $uut = $this->app->make("module.service.route_source");
        $this->assertSame("console", $uut->getConsoleRoute());
    }

    public function testGetChannelsRoute () : void
    {
        $uut = $this->app->make("module.service.route_source");
        $this->assertSame("channels", $uut->getChannelsRoute());
    }

    public function testGetRouteRootDir () : void
    {
        $uut = $this->app->make("module.service.route_source");
        $this->assertSame("routes", $uut->getRouteRootDir());
    }

    public function testGetRouteFileExtension () : void
    {
        $uut = $this->app->make("module.service.route_source");
        $this->assertSame(".php", $uut->getRouteFileExtension());
    }

    public function testGetRouteFiles () : void
    {
        $uut = $this->app->make("module.service.route_source");
        $this->assertSame([
            "web",
            "api",
            "console",
            "channels",
        ], $uut->getRouteFiles());
    }
}
