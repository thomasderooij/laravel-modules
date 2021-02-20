<?php

declare(strict_types=1);

namespace Thomasderooij\LaravelModules\Exceptions\DependencyExceptions;

use Throwable;

abstract class DependencyException extends \Exception
{
    protected $upstream;
    protected $downstream;

    public function __construct(string $downstream, string $upstream, $message = "", $code = 0, Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);

        $this->downstream = $downstream;
        $this->upstream = $upstream;
    }
}
