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

    public function hydrate(array $dto, string $class, array $rules)
    {
        return $this->hydrator->hydrate($dto, $class, $rules);
    }

    public function hydrateModel(array $dto, object $model, array $rules): void
    {
        $this->hydrator->hydrateModel($dto, $model, $rules);
    }

    public function extract(object $model, array $rules = []): array
    {
        return $this->extractor->extract($model, $rules);
    }
}
