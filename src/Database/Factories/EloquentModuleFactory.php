<?php

namespace Thomasderooij\LaravelModules\Database\Factories;

use Faker\Generator as Faker;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;
use Thomasderooij\LaravelModules\Contracts\Services\ModuleManager;

abstract class EloquentModuleFactory extends Factory
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

    /**
     * Get the factory name for the given model name.
     *
     * @param  string  $modelName
     * @return string
     */
    public static function resolveFactoryName(string $modelName)
    {
        $resolver = static::$factoryNameResolver ?: function (string $modelName) {
            $appNamespace = static::appNamespace();

            $modelName = Str::startsWith($modelName, $appNamespace.'Models\\')
                ? Str::after($modelName, $appNamespace.'Models\\')
                : Str::after($modelName, $appNamespace);

            return static::$namespace.$modelName.'Factory';
        };

        return $resolver($modelName);
    }
}
