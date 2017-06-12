<?php
namespace PTS\Hydrator;

class NormalizerRule
{

    public function normalize(array $rule, string $dtoProperty): array
    {
        return array_merge([
            'pipe' => [],
            'prop' => $dtoProperty
        ], $rule);
    }
}
