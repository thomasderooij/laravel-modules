<?php

declare(strict_types=1);

namespace Thomasderooij\LaravelModules\CompositeProviders;

use App\Providers\EventServiceProvider;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

class EventCompositeServiceProvider extends ServiceProvider
{
    use CompositeProviderTrait;

    protected string $name = "EventServiceProvider";
    protected $listen = [];

    public function listens(): array
    {
        // Merge all the provider listens into one listen property
        foreach ($this->providers as $providerClass) {
            /** @var EventServiceProvider $provider */
            $provider = new $providerClass($this->app);
            $this->listen = array_merge($this->listen, $provider->listens());
        }

        return parent::listens();
    }
}
