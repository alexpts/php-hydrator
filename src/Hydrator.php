<?php

namespace PTS\Hydrator;

class Hydrator
{
    /** @var array */
    protected $reflectionCache = [];

    /** @var \Closure */
    protected $populateClosure;

    public function __construct(HydrateClosure $hydrateFn = null)
    {
        $fn = $hydrateFn ?? new HydrateClosure;
        $this->populateClosure = $fn->populateClosure();
    }

    public function hydrate(array $dto, string $class, array $rules)
    {
        $model = $this->createModel($class);
        $this->hydrateModel($dto, $model, $rules);

        return $model;
    }

    public function hydrateModel(array $dto, object $model, array $rules): void
    {
        $this->populateClosure->call($model, $dto, $rules);
    }

    protected function createModel(string $class): object
    {
        $reflection = $this->getReflection($class);
        return $reflection->newInstanceWithoutConstructor();
    }

    protected function getReflection(string $class): \ReflectionClass
    {
        $hasCache = $this->reflectionCache[$class] ?? false;
        if (!$hasCache) {
            $this->reflectionCache[$class] = new \ReflectionClass($class);
        }

        return $this->reflectionCache[$class];
    }
}
