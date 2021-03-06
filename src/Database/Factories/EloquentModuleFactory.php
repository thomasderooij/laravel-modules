<?php

namespace Thomasderooij\LaravelModules\Database\Factories;

use Faker\Generator as Faker;
use Illuminate\Database\Eloquent\Factory;
use Thomasderooij\LaravelModules\Contracts\Services\ModuleManager;

class EloquentModuleFactory extends Factory
{
    public static function construct(Faker $faker, $pathToFactories = null)
    {
        $instance = new static($faker);

        $pathToFactories = $pathToFactories ?: database_path('factories');
        $instance->load($pathToFactories);

        /** @var ModuleManager $moduleManager */
        $moduleManager = app()->make("module.service.manager");
        if ($moduleManager->isInitialised()) {
            /** @var ModuleManager $manager */
            $manager = app()->make("module.service.manager");

            foreach ($moduleManager->getActiveModules() as $module) {
                $path = $manager->getModuleDirectory($module) . "/database/factories";
                $instance->load($path);
            }
        }

        return $instance;
    }
}
