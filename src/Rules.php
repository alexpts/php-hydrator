<?php

namespace PTS\Hydrator;

class Rules
{
    /** @var array */
    public $rules = [];

    public function __construct(array $rules)
    {
        $this->rules = $this->normalize($rules);
    }

    protected function normalize(array $rules): array
    {
        foreach ($rules as $name => &$rule) {
            $rule['pipe'] = $rule['pipe'] ?? [];
            $rule['prop'] = $rule['prop'] ?? $name;
        }

        return $rules;
    }
}
