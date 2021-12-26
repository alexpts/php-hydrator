<?php
declare(strict_types=1);

namespace PTS\Hydrator;

use Closure;
use function is_array;

class HydrateClosure
{

    public function populateClosure(): Closure
    {
        return static function(object $model, array $dto, array $rules)
        {
            foreach ($dto as $name => $value) {
                $rule = $rules[$name] ?? null;
                if ($rule === null) {
                    continue;
                }

                $setter = $rule['set'] ?? false;
                if (!$setter) {
                    $model->{$rule['prop']} = $value;
                    continue;
                }

                if (is_array($setter)) {
                    [$method, $args] = $setter;
                    array_unshift($args, $value);
                    $model->$method(...$args);
                } else {
                    $model->$setter($value);
                }
            }

            return $model;
        };
    }
}
