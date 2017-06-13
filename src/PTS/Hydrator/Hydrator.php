<?php

namespace PTS\Hydrator;

class Hydrator
{
    /** @var array */
    protected $reflectionCache = [];
    /** @var NormalizerRule */
    protected $normalizer;
    /** @var HydrateClosure */
    protected $hydrateFn;

    public function __construct(HydrateClosure $hydrateFn, NormalizerRule $normalizer)
    {
        $this->hydrateFn = $hydrateFn;
        $this->normalizer = $normalizer;
    }

    public function hydrate(array $dto, string $class, array $rules)
    {
        $model = $this->createModel($class);
        $this->hydrateModel($dto, $model, $rules);

        return $model;
    }

    public function hydrateModel(array $dto, $model, array $rules): void
    {
        foreach ($dto as $name => $val) {
            if (!array_key_exists($name, $rules)) {
                continue;
            }

            $rule = $this->normalizer->normalize($rules[$name], $name);
            $value = $this->hydratePipe($val, $rule['pipe']);
            $this->fillFieldValue($value, $rule, $model);
        }
    }

    protected function fillFieldValue($value, array $rule, $model): void
    {
        array_key_exists('set', $rule)
            ? $this->hydrateFn->getHydrateSetterFn()->call($model, $rule['set'], $value)
            : $this->hydrateFn->getHydratePropertyFn()->call($model, $rule['prop'], $value);
    }

    /**
     * @param mixed $value
     * @param array $pipes
     * @return mixed
     */
    protected function hydratePipe($value, array $pipes)
    {
        foreach ($pipes as $filter) {
            $value = $this->applyFilter($value, $filter);
        }

        return $value;
    }

    /**
     * @param mixed $value
     * @param callable|array $filter
     * @return mixed
     */
    protected function applyFilter($value, $filter)
    {
        if (is_callable($filter)) {
            return $filter($value);
        }

        if (is_array($filter) && array_key_exists('hydrate', $filter)) {
            $value = $filter['hydrate']($value);
        }

        return $value;
    }

    protected function createModel(string $class)
    {
        $reflection = $this->getReflection($class);
        return $reflection->newInstanceWithoutConstructor();
    }

    protected function getReflection(string $class): \ReflectionClass
    {
        if (!array_key_exists($class, $this->reflectionCache)) {
            $this->reflectionCache[$class] = new \ReflectionClass($class);
        }

        return $this->reflectionCache[$class];
    }
}