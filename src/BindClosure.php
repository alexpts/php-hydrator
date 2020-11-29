<?php
declare(strict_types=1);

namespace PTS\Hydrator;

use Closure;

class BindClosure
{
    /** @var Closure[] */
    protected array $fnCache = [];
    protected Closure $fn;

    protected function createFn(string $class): Closure
    {
        $this->fnCache[$class] = Closure::bind($this->fn, null, $class);
        return $this->fnCache[$class];
    }
}
