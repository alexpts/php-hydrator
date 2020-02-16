<?php

namespace PTS\Hydrator;

class Extractor extends BindClosure implements ExtractorInterface
{

    public function __construct(ExtractClosure $extractor = null)
    {
        $extractor = $extractor ?? new ExtractClosure;
        $this->fn = $extractor->extractClosure();
    }

    public function extract(object $model, array $rules): array
    {
        $class = get_class($model);
        $fn = $this->fnCache[$class] ?? $this->createFn($class);
        return $fn($model, $rules);
    }
}
