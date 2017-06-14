<?php

namespace PTS\Hydrator;

class DataTransformer
{
    /** @var HydratorService */
    protected $hydratorService;
    /** @var MapsManager */
    protected $mapsManager;

    public function __construct(HydratorService $hydratorService, MapsManager $mapsManager)
    {
        $this->hydratorService = $hydratorService;
        $this->mapsManager = $mapsManager;
    }

    public function toModel(array $dto, string $class, string $mapName = 'dto')
    {
        $rules = $this->mapsManager->getMap($class, $mapName);

        return $this->hydratorService->hydrate($dto, $class, $rules);
    }

    public function fillModel(array $dto, $model, string $mapName = 'dto'): void
    {
        $rules = $this->mapsManager->getMap(get_class($model), $mapName);

        $this->hydratorService->hydrateModel($dto, $model, $rules);
    }

    public function toDTO($model, string $mapName = 'dto', array $excludeFields = []): array
    {
        $rules = $this->mapsManager->getMap(get_class($model), $mapName);

        foreach ($excludeFields as $field) {
            unset($rules[$field]);
        }

        return $this->hydratorService->extract($model, $rules);
    }

    public function getMapsManager(): MapsManager
    {
        return $this->mapsManager;
    }
}
