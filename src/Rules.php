<?php

namespace PTS\Hydrator;

class Rules
{
    /** @var array */
    protected $rules = [];

    public function __construct(array $rules)
    {
        $this->rules = $this->normalize($rules);
    }

    public function getRules(): array
    {
        return $this->rules;
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
