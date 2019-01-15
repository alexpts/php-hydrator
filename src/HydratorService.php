<?php

namespace PTS\Hydrator;

class HydratorService implements HydratorInterface
{
    /** @var Extractor */
    protected $extractor;
    /** @var Hydrator */
    protected $hydrator;

    public function __construct(Extractor $extractor = null, Hydrator $hydrator = null)
    {
        $this->extractor = $extractor ?? new Extractor;
        $this->hydrator = $hydrator ?? new Hydrator;
    }

    public function hydrate(array $dto, string $class, Rules $rules)
    {
        return $this->hydrator->hydrate($dto, $class, $rules->getRules());
    }

    public function hydrateModel(array $dto, object $model, Rules $rules): void
    {
        $this->hydrator->hydrateModel($dto, $model, $rules->getRules());
    }

    public function extract(object $model, Rules $rules): array
    {
        return $this->extractor->extract($model, $rules->getRules());
    }
}
