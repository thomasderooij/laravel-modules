<?php

declare(strict_types=1);

namespace Thomasderooij\LaravelModules\Tests\Database;

use Illuminate\Database\Eloquent\Factories\Factory;
use Thomasderooij\LaravelModules\Database\Factories\EloquentModuleFactory;

trait HasFactory
{
    /**
     * Get a new factory instance for the model.
     *
     * @param  mixed  $parameters
     * @return Factory
     */
    public static function factory(...$parameters)
    {
        $factory = static::newFactory() ?: EloquentModuleFactory::factoryForModel(get_called_class());

        return $factory
            ->count(is_numeric($parameters[0] ?? null) ? $parameters[0] : null)
            ->state(is_array($parameters[0] ?? null) ? $parameters[0] : ($parameters[1] ?? []));
    }

    /**
     * Create a new factory instance for the model.
     *
     * @return Factory
     */
    protected static function newFactory()
    {
        //
    }
}
