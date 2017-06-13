<?php
declare(strict_types = 1);

namespace PTS\Hydrator;

class HydrateClosure
{
    /** @var \Closure */
    protected $hydratePropertyFn;
    /** @var \Closure */
    protected $hydrateSetterFn;

    public function getHydratePropertyFn(): \Closure
    {
        if ($this->hydratePropertyFn === null) {
            $this->hydratePropertyFn = $this->createPropertyClosure();
        }

        return $this->hydratePropertyFn;
    }

    public function getHydrateSetterFn(): \Closure
    {
        if ($this->hydrateSetterFn === null) {
            $this->hydrateSetterFn = $this->createSetterClosure();
        }

        return $this->hydrateSetterFn;
    }

    protected function createPropertyClosure(): \Closure
    {
        return function(string $property, $value) {
            if (property_exists($this, $property)) {
                $this->{$property} = $value;
            }
        };
    }

    protected function createSetterClosure(): \Closure
    {
        /**
         * @param string|array $setter
         * @param mixed $value
         * @throws HydratorException
         */
        return function($setter, $value) {
            list($method, $args) = is_array($setter)
                ? [$setter[0], $setter[1]]
                : [$setter, []];

            if (!is_callable([$this, $method])) {
                throw new HydratorException('Getter key is not callable');
            }

            array_unshift($args, $value);
            call_user_func_array([$this, $method], $args);
        };
    }
}
