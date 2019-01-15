<?php

namespace PTS\Hydrator;

interface HydratorInterface
{

    public function hydrate(array $dto, string $class, Rules $rules);

    public function hydrateModel(array $dto, object $model, Rules $rules): void;

    public function extract(object $model, Rules $rules): array;
}
