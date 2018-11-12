<?php
declare(strict_types = 1);

namespace PTS\Hydrator;

class ExtractClosure
{
    /** @var \Closure */
    protected $propertyFn;
    /** @var \Closure */
    protected $getterFn;

    public function getPropertyFn(): \Closure
    {
        if ($this->propertyFn === null) {
            $this->propertyFn = $this->propertyClosure();
        }

        return $this->propertyFn;
    }

    public function getGetterFn(): \Closure
    {
        if ($this->getterFn === null) {
            $this->getterFn = $this->getterClosure();
        }

        return $this->getterFn;
    }

    protected function propertyClosure(): \Closure
    {
        return function(string $property) {
            return property_exists($this, $property) ? $this->{$property} : null;
        };
    }

    protected function getterClosure(): \Closure
    {
        /**
         * @param string|array $getter
         * @return mixed
         */
        return function($getter) {
        	$action = \is_array($getter) ? $getter[0] : $getter;
        	$params = \is_array($getter) ? $getter[1] : [];

            if (!\is_callable([$this, $action])) {
                throw new ExtractorException('Getter is not callable');
            }

            return \call_user_func_array([$this, $action], $params);
        };
    }
}
