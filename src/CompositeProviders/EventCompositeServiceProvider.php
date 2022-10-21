<?php

namespace Thomasderooij\LaravelModules\CompositeProviders;

use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

class EventCompositeServiceProvider extends ServiceProvider
{
    use CompositeProviderTrait;

    protected $name = "EventServiceProvider";
    protected $listen = [];

    public function listens ()
    {
        // Merge all the provider listens into one listen property
        foreach ($this->providers as $providerClass) {
            $provider = new $providerClass($this->app);
            $this->listen = array_merge($this->listen, $provider->listens());
        }

        return parent::listens();
    }
}
