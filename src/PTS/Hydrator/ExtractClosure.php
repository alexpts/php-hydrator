<?php
declare(strict_types = 1);

namespace PTS\Hydrator;

class ExtractClosure
{
    /** @var \Closure */
    protected $extractPropertyFn;
    /** @var \Closure */
    protected $extractGetterFn;

    public function getExtractPropertyFn(): \Closure
    {
        if ($this->extractPropertyFn === null) {
            $this->extractPropertyFn = $this->createPropertyClosure();
        }

        return $this->extractPropertyFn;
    }

    public function getExtractGetterFn(): \Closure
    {
        if ($this->extractGetterFn === null) {
            $this->extractGetterFn = $this->createGetterClosure();
        }

        return $this->extractGetterFn;
    }

    protected function createPropertyClosure(): \Closure
    {
        return function(string $property) {
            return property_exists($this, $property) ? $this->{$property} : null;
        };
    }

    protected function createGetterClosure(): \Closure
    {
        /**
         * @param string|array $getter
         * @return mixed
         * @throws ExtractorException
         */
        return function($getter) {
            [$method, $args] = is_array($getter)
                ? [$getter[0], $getter[1]]
                : [$getter, []];

            if (!is_callable([$this, $method])) {
                throw new ExtractorException('Getter key is not callable');
            }

            return call_user_func_array([$this, $method], $args);
        };
    }
}
