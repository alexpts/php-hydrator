<?php
declare(strict_types=1);

namespace PTS\Hydrator;

class ExtractClosure
{

    public function extractClosure(): \Closure
    {
        return function(array $rules): array
        {
            $dto = [];

            foreach ($rules as $name => $rule) {
                $getter = $rule['get'] ?? false;
                if (!$getter) {
                    $prop = $rule['prop'];
                    $dto[$name] = $this->{$prop};
                    continue;
                }

                $isArray = \is_array($getter);
                $action = $isArray ? $getter[0] : $getter;
                $params = $isArray ? $getter[1] : [];

                $dto[$name] = \call_user_func_array([$this, $action], $params);
            }

            return $dto;
        };
    }
}
