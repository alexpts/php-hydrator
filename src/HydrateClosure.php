<?php
declare(strict_types=1);

namespace PTS\Hydrator;

class HydrateClosure
{

    public function populateClosure(): \Closure
    {
        return function (array $dto, array $rules): void {
            foreach ($dto as $name => $value) {
                $rule = $rules[$name] ?? null;
                if ($rule === null) {
                    continue;
                }

                $setter = $rule['set'] ?? false;
                if (!$setter) {
                    $prop = $rule['prop'];
                    $this->{$prop} = $value;
                    continue;
                }

                $isArray = \is_array($setter);
                $method = $isArray ? $setter[0] : $setter;
                $args = $isArray ? $setter[1] : [];

                if (!\is_callable([$this, $method])) {
                    throw new HydratorException('Getter key is not callable');
                }

                array_unshift($args, $value);
                \call_user_func_array([$this, $method], $args);
            }
        };
    }
}
