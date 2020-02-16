<?php
declare(strict_types=1);

namespace PTS\Hydrator;

interface HydratorInterface
{

    public function hydrate(array $dto, string $class, array $rules);

    public function hydrateModel(array $dto, object $model, array $rules): void;
}
