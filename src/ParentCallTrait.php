<?php

declare(strict_types=1);

namespace Thomasderooij\LaravelModules;

trait ParentCallTrait
{
    /**
     * Call the parent with a function and arguments. Honestly, this only exists for partial tests where you want to
     *  confirm your unit under tests calls the parent call.
     *
     * @param string $function
     * @param array $args
     * @return mixed
     */
    protected function parentCall(string $function, array $args = [])
    {
        return parent::$function(...$args);
    }
}
