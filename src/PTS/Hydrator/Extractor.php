<?php

namespace PTS\Hydrator;

class Extractor
{
    /** @var ExtractClosure */
    protected $fn;
    /** @var NormalizerRule */
    protected $normalizer;

    public function __construct(ExtractClosure $extractor = null, NormalizerRule $normalizer = null)
    {
        $this->fn = $extractor ?? new ExtractClosure;
        $this->normalizer = $normalizer ?? new NormalizerRule;
    }

    public function extract(object $model, array $rules): array
    {
        $dto = [];

        foreach ($rules as $dtoKey => $rawRule) {
            $rule = $this->normalizer->normalize($rawRule, $dtoKey);
            $val = $this->extractFieldValue($rule, $model);

            $dto[$dtoKey] = $val !== null
                ? $this->extractPipe($val, $rule['pipe'])
                : null;
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
            if (\is_callable($filter)) {
                $val = $filter($val);
            } else if (\is_array($filter) && array_key_exists('extract', $filter)) {
                $val = $filter['extract']($val);
            }
        }

        return $val;
    }

    protected function extractFieldValue(array $rule, object $model)
    {
        return array_key_exists('get', $rule)
            ? $this->fn->getGetterFn()->call($model, $rule['get'])
            : $this->fn->getPropertyFn()->call($model, $rule['prop']);
    }
}
