# php-hydrator

[![SensioLabsInsight](https://insight.sensiolabs.com/projects/de0407d9-12fe-4d3d-a688-9b29b10a0e46/big.png)](https://insight.sensiolabs.com/projects/de0407d9-12fe-4d3d-a688-9b29b10a0e46)

[![Build Status](https://travis-ci.org/alexpts/php-hydrator.svg?branch=master)](https://travis-ci.org/alexpts/php-hydrator)
[![Code Coverage](https://scrutinizer-ci.com/g/alexpts/php-hydrator/badges/coverage.png?b=master)](https://scrutinizer-ci.com/g/alexpts/php-hydrator/?branch=master)
[![Code Climate](https://codeclimate.com/github/alexpts/php-hydrator/badges/gpa.svg)](https://codeclimate.com/github/alexpts/php-hydrator)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/alexpts/php-hydrator/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/alexpts/php-hydrator/?branch=master)


Одни и те же данные нужно представить в разном виде. В коде удобно работать с высокоуровневыми моделями. Но для сохранения этих данных в базу данных, как правило, данные требуется перевести в более простой вид, обычно в ассоциативный массив. Для передачи данных между приложениями используют простые DTO сущности.

Компонент позволяет легко конвертировать ваши данные в ассоциативный массив из модели и обратно обратно заполнять вашу модель данными.

### Extractor
Задача класса Extractor извлечь из вашей модели данные согласно указанынм правилам.

```php
$extractor = new Extractor(new ExtractClosure, new NormalizerRule);

$model = new Model([
    'id' => 1,
    'name' => 'Alex'
    'email' => 'some@web.dev'
]);

$extractor->extract($model, [
    'id' => [], // prop as dto`s key 
    'name' => [
        'prop' => 'name', // prop is name field in model
    ],
    'email' => [
        'get' => 'getEmail', // getter $model->getEmail();
    ]
])
```

Правила извлечения данных описываются в виде ассоциативного массива, где ключ массива это имя ключа в DTO сущности.
Наприимер поле модели name можно замапить в поле с именем login в DTO сущности таким образом.

```php

$extractor->extract($model, [
    'login' => [
        'prop' => 'name',
    ],
    ...
])
```

Извлечение через prop позволяет извлеч из модели поле с любой обрастью видимости (public/protect/private).
Если значение prop не указано явно, то оно равно имени ключа DTO сущности. В следующем примере это будет значение name.
```php

$extractor->extract($model, [
    'name' => [],
    ...
])
```

Помимо извлечения данных свойств из моделе, данные можно получить через вызов метода модели (getter).
```php
$extractor->extract($model, [
    'name' => [
        'get' => 'getName', // getter $model->getName();
    ],
])
```

Геттер имеит более высокий приоритет, чем свойтво prop.

### Extractor Pipes
Помимо извлечения даннх из модели можно к каждому извлеченному значению применять фильтры.
Например явно конвертировать тип данных можно так:

```php

$extractor->extract($model, [
    'name' => [
        'pipe' => ['strval', 'trim'],
    ],
    'age' => [
       'pipe' => ['intval'],
    ],
])
```

Для каждого поля описывается поле `pipe` в виде массива. Каждый элемент этого массва представлят собой callable тип, через которое пройдет значение.
Порядок сохраняется обхявлению pipe фильтров.

Перед сохранением поля типа \DateTime в базу данных зачастую нужно преобразовать тип в timestamp или иной. Это делается так:
```php

$extractor->extract($model, [
    'creAt' => [
        'pipe' => [function (\DateTime $value) {
            return $value->getTimestamp();
        }],
    ]
])
```

### Hydrator
Класс Hydrator позволяет наполнить модель данными.


```php
$hydrator = new Hydrator(new HydrateClosure, new NormalizerRule);

$dto = [
    'id' => 1,
    'login' => 'Alex'
    'email' => 'some@web.dev
];

$model = $extractor->hydrate($dto, Model::class, [
    'id' => [], // prop as dto`s key 
    'login' => [
        'prop' => 'name', // dto key login fill property name
    ],
    'email' => [
        'set' => 'setEmail', // setter $model->setEmail();
    ]
]);

$model2 = new Model;
$extractor->hydrateModel($dto, $model2, [
    'id' => [], // prop as dto`s key 
    'login' => [
        'prop' => 'name', // dto key login fill property name
    ],
    'email' => [
        'set' => 'setEmail', // setter $model->setEmail();
    ]
])
```

Правила гидрации точно такие же как и у extractor сущности.

Точно также можно применять pipe преобразователи, при заполнении модели.
Чтобы создать конвертировать timestamp в объект \DateTime при наполеннии модели, нужно использовать подобный pipe:

```php

$hydrator->hydratModel($dto, $model, [
    'creAt' => [
        'pipe' => [function (int $value) {
            return new \DateTime('@' . $value);
        }],
    ]
])
```

### HydratorService
Класс HydratorService является простой оберткой над Hydrator и Extractor и является более высокоуровневым.
```php
$hydratorService = new HydratorService($hydrator, $extractor);
$rules = [
    'id' => [], // prop as dto`s key 
    'login' => [
        'prop' => 'name', // dto key login fill property name
    ],
    'email' => [
        'set' => 'setEmail', // setter $model->setEmail();
    ]
];

$dto = $hydratorService->extract($model, $rules);
$model = $hydratorService->hydrate($dto, Model::class, $rules);
```

Для обратного конвертирования данных из модели в DTO сущносить и обратно можно использовать один набор правил преобразования.
За счет чего мы можем описывать правила преобразования декларативно (кроме анонимных функций).

Все Pipe фильтры срабатываю в обе стороны, чтобы разделить фильтры, можно использовать для фидььра немного иной формат записи.
```php

$rules = [
    'creAte' => [
        [
            'hydrate' => function(int $timestamp) {
                return new \DateTime('@' . $timestamp);
            },
            'extract' => functuin(\DateTime $date) {
                return $date->getTimestamp();
            }
        ],
        'someGeneralFilter',
        function ($value) {
            // general pipe for both convert
            ...
            return $value;
        }
    ],
];

$dto = $hydratorService->extract($model, $rules);
$model = $hydratorService->hydrate($dto, Model::class, $rules);

```


### Data Transformer
Класс DataTransformer является еще более высокого уровнемвым. Он позволяет работать с HydratorService и описывать схемы преобразования для каждого класса отдельно.

Для одного класса может быть множество схем преобразования. Например для преобразования модели для сохранения в БД требуется преобразовать ее в DTO сущность.
При этом все значения типа \DateTime преобразовать в timestamp. 

Но если мы передаем эту же модель на клиент через REST API, то схема преобразования может быть иной.
Все значения \DateTime нужно представить в виде строки в формате ISO8601. А еще у нас может быть просто более компактное представлеиние этой же модели, без лишних деталей.


```php
$normalizeRule = new NormalizerRule;
$extractor = new Extractor(new ExtractClosure, $normalizeRule);
$hydrator = new Hydrator(new HydrateClosure, $normalizeRule);
$hydratorService = new HydratorService($extractor, $hydrator);

$mapsManager = new MapsManager;
$mapsManager->setMapDir(UserModel::class, __DIR__ . '/data');

$dataTransformer = new DataTransformer($hydratorService, $mapsManager);

$model = $dataTransformer->toModel([
    'id' => 1,
    'creAt' => new \DateTime,
    'name' => 'Alex',
    'active' => 1,
], UserModel::class);

$dto = $dataTransformer->toDTO($model);
$shortFormatDto = $dataTransformer->toDTO($model, 'short.dto');
```
