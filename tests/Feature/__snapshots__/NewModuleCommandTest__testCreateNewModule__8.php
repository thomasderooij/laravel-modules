<?php return '<?php

namespace Root\\NewModule\\Providers;

use Illuminate\\Foundation\\Support\\Providers\\AuthServiceProvider as ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array
     */
    protected $policies = [];

    /**
     * Register any authentication / authorization services.
     *
     * @return void
     */
    public function boot()
    {

    }

    private function registerGates () : void
    {

    }
}
';
