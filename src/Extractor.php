<?php

namespace PTS\Hydrator;

class Extractor
{

    /** @var \Closure */
    protected $extractFn;

    public function __construct(ExtractClosure $extractor = null)
    {
        $extractor = $extractor ?? new ExtractClosure;
        $this->extractFn = $extractor->extractClosure();
    }

    public function extract(object $model, array $rules): array
    {
        return $this->extractFn->call($model, $rules);
    }
}
