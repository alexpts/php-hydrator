<?php

namespace PTS\Hydrator;

class HydratorService
{
    /** @var Extractor */
    protected $extractor;
    /** @var Hydrator */
    protected $hydrator;

    public function __construct(Extractor $extractor, Hydrator $hydrator)
    {
        $this->extractor = $extractor;
        $this->hydrator = $hydrator;
    }

    public function hydrate(array $dto, string $class, array $rules)
    {
        return $this->hydrator->hydrate($dto, $class, $rules);
    }

    public function hydrateModel(array $dto, $model, array $rules): void
    {
        $this->hydrator->hydrateModel($dto, $model, $rules);
    }

    public function extract($model, array $excludeFields = []): array
    {
        return $this->extractor->extract($model, $excludeFields);
    }
}
