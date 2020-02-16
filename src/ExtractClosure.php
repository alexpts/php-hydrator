<?php
declare(strict_types=1);

namespace PTS\Hydrator;

use Closure;
use function call_user_func_array;
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

                $isArray = is_array($getter);
                $action = $isArray ? $getter[0] : $getter;
                $params = $isArray ? $getter[1] : [];

                $dto[$name] = call_user_func_array([$model, $action], $params);
            }

            return $dto;
        };
    }
}
