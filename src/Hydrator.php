<?php
declare(strict_types=1);

namespace PTS\Hydrator;

use Closure;
use ReflectionClass;
use ReflectionException;

class Hydrator implements HydratorInterface
{
    /** @var array */
    protected $emptyModels = [];

    /** @var Closure */
    protected $populateClosure;

    public function __construct(HydrateClosure $hydrateFn = null)
    {
        $fn = $hydrateFn ?? new HydrateClosure;
        $this->populateClosure = $fn->populateClosure();
    }

    public function hydrate(array $dto, string $class, array $rules)
    {
        $model = $this->emptyModels[$class] ?? $this->createModel($class);
        $this->populateClosure->call($model, $dto, $rules);

        return $model;
    }

    public function hydrateModel(array $dto, object $model, array $rules): void
    {
        $this->populateClosure->call($model, $dto, $rules);
    }

    /**
     * @param string $class
     *
     * @return object
     * @throws ReflectionException
     */
    protected function createModel(string $class)
    {
        $reflection = new ReflectionClass($class);
        $this->emptyModels[$class] = $reflection->newInstanceWithoutConstructor();
        return $this->emptyModels[$class];
    }
}
