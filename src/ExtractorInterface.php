<?php
declare(strict_types=1);

namespace PTS\Hydrator;

interface ExtractorInterface
{

    public function extract(object $model, array $rules): array;
}
