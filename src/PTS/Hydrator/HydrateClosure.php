<?php
declare(strict_types = 1);

namespace PTS\Hydrator;

class HydrateClosure
{
    /** @var \Closure */
    protected $propertyFn;
    /** @var \Closure */
    protected $setterFn;

    public function getPropertyFn(): \Closure
    {
        if ($this->propertyFn === null) {
            $this->propertyFn = $this->propertyClosure();
        }

        return $this->propertyFn;
    }

    public function getSetterFn(): \Closure
    {
        if ($this->setterFn === null) {
            $this->setterFn = $this->setterClosure();
        }

        return $this->setterFn;
    }

    protected function propertyClosure(): \Closure
    {
        return function(string $property, $value) {
            $this->{$property} = $value;
        };
    }

    protected function setterClosure(): \Closure
    {
        /**
         * @param string|array $setter
         * @param mixed $value
         */
        return function($setter, $value) {
        	$method = \is_array($setter) ? $setter[0] : $setter;
	        $args = \is_array($setter) ? $setter[1] : [];

            if (!\is_callable([$this, $method])) {
                throw new HydratorException('Getter key is not callable');
            }

            array_unshift($args, $value);
            \call_user_func_array([$this, $method], $args);
        };
    }
}
