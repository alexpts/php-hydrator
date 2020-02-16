<?php

namespace PTS\Hydrator;

use Closure;

class BindClosure
{
    /** @var Closure[] */
    protected $fnCache = [];
    /** @var Closure */
    protected $fn;

    protected function createFn(string $class): Closure
    {
        $this->fnCache[$class] = Closure::bind($this->fn, null, $class);
        return $this->fnCache[$class];
    }
}
