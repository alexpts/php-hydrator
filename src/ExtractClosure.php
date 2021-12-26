<?php
declare(strict_types=1);

namespace PTS\Hydrator;

use Closure;
use function is_array;

class ExtractClosure
{

    public function extractClosure(): Closure
    {
        return static function($model, array $rules): array
        {
            $dto = [];

            foreach ($rules as $name => $rule) {
                $getter = $rule['get'] ?? false;
                if (!$getter) {
                    $prop = $rule['prop'];
                    $dto[$name] = $model->{$prop};
                    continue;
                }

                if (is_array($getter)) {
                    $action = $getter[0];
                    $params = $getter[1] ?? [];
                    $dto[$name] = $model->$action(...$params);
                } else {
                    $dto[$name] = $model->$getter();
                }
            }

            return $dto;
        };
    }
}
