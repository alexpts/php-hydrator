<?php
declare(strict_types=1);

namespace PTS\Hydrator;

class Normalizer
{

    public function normalize(array $rules): array
    {
        foreach ($rules as $name => &$rule) {
            $rule['prop'] = $rule['prop'] ?? $name;
        }

        return $rules;
    }
}
