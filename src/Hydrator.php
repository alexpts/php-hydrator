<?php
declare(strict_types=1);

namespace PTS\Hydrator;

use ReflectionClass;
use ReflectionException;

class Hydrator extends BindClosure implements HydratorInterface
{
    /** @var object[] */
    protected array $emptyModels = [];

    public function __construct(HydrateClosure $hydrateFn = null)
    {
        $fn = $hydrateFn ?? new HydrateClosure;
        $this->fn = $fn->populateClosure();
    }

    public function hydrate(array $dto, string $class, array $rules): object
    {
        $model = $this->emptyModels[$class] ?? $this->createModel($class);
        $fn = $this->fnCache[$class] ?? $this->createFn($class);
        return $fn(clone $model, $dto, $rules);
    }

    public function hydrateModel(array $dto, object $model, array $rules): void
    {
        $class = get_class($model);
        $fn = $this->fnCache[$class] ?? $this->createFn($class);
        $fn($model, $dto, $rules);
    }

    /**
     * @param string $class
     *
     * @return object
     * @throws ReflectionException
     */
    protected function createModel(string $class): object
    {
        $reflection = new ReflectionClass($class);
        $this->emptyModels[$class] = $reflection->newInstanceWithoutConstructor();
        return $this->emptyModels[$class];
    }
}
