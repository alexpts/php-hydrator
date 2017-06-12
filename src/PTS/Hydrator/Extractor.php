<?php

namespace PTS\Hydrator;

class Extractor
{
    /** @var ExtractClosure */
    protected $extractFn;
    /** @var NormalizerRule */
    protected $normalizer;

    public function __construct(ExtractClosure $extractor, NormalizerRule $normalizer)
    {
        $this->extractFn = $extractor;
        $this->normalizer = $normalizer;
    }

    public function extract($model, array $rules): array
    {
        $dto = [];

        foreach ($rules as $dtoKey => $rawRule) {
            $rule = $this->normalizer->normalize($rawRule, $dtoKey);
            $val = $this->extractFieldValue($rule, $model);

            if ($val !== null) {
                $dto[$dtoKey] = $this->extractPipe($val, $rule['pipe']);
            }
        }

        return $dto;
    }

    /**
     * @param mixed $val
     * @param array $pipes
     * @return mixed
     */
    protected function extractPipe($val, array $pipes)
    {
        foreach ($pipes as $filter) {
            if (is_callable($filter)) {
                $val = $filter($val);
            } else if (is_array($filter) && array_key_exists('extract', $filter)) {
                $val = $filter['extract']($val);
            }
        }

        return $val;
    }

    protected function extractFieldValue(array $rule, $model)
    {
        return array_key_exists('get', $rule)
            ? $this->extractFn->getExtractGetterFn()->call($model, $rule['get'])
            : $this->extractFn->getExtractPropertyFn()->call($model, $rule['prop']);
    }
}