# php-hydrator

[![SensioLabsInsight](https://insight.sensiolabs.com/projects/de0407d9-12fe-4d3d-a688-9b29b10a0e46/big.png)](https://insight.sensiolabs.com/projects/de0407d9-12fe-4d3d-a688-9b29b10a0e46)

[![Build Status](https://travis-ci.org/alexpts/php-hydrator.svg?branch=master)](https://travis-ci.org/alexpts/php-hydrator)
[![Code Coverage](https://scrutinizer-ci.com/g/alexpts/php-hydrator/badges/coverage.png?b=master)](https://scrutinizer-ci.com/g/alexpts/php-hydrator/?branch=master)
[![Code Climate](https://codeclimate.com/github/alexpts/php-hydrator/badges/gpa.svg)](https://codeclimate.com/github/alexpts/php-hydrator)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/alexpts/php-hydrator/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/alexpts/php-hydrator/?branch=master)


Одни и те же данные нужно представить в разном виде. В коде удобно работать с высокоуровневыми моделями. Но для сохранения этих данных в базу данных, как правило, данные требуется перевести в более простой вид, обычно в ассоциативный массив. Для передачи данных между приложениями используют простые DTO сущности.

Компонент позволяет легко конвертировать ваши данные в ассоциативный массив из модели и обратно заполнять вашу модель данными.

### Extractor
Задача класса Extractor извлечь из вашей модели данные согласно указанынм правилам.

```php
$extractor = new Extractor;
$normalizer = new \PTS\Hydrator\Normalizer;

$model = new Model([
    'id' => 1,
    'name' => 'Alex'
    'email' => 'some@web.dev'
]);

$rules = [
   'id' => [], // prop as dto`s key
   'name' => [
	   'prop' => 'name', // prop is name field in model
   ],
   'email' => [
	   'get' => 'getEmail', // getter $model->getEmail();
   ]
];
$rules = $normalizer->normalize($rules);

$extractor->extract($model, $rules)
```

Правила извлечения данных описываются в виде ассоциативного массива, где ключ массива это имя ключа в DTO сущности.
Наприимер поле модели name можно замапить в поле с именем login в DTO сущности таким образом.

```php

$rules = [
   'login' => [
	   'prop' => 'name',
   ],
   ...
];
$extractor->extract($model, $rules);
```

Извлечение через prop позволяет извлеч из модели поле с любой областью видимости (public/protect/private).
Если значение prop не указано явно, то оно равно имени ключа DTO сущности. В следующем примере это будет значение name.
```php

$rules = [
   'name' => [],
   ...
];
$rules = $normalizer->normalize($rules);

$extractor->extract($model, $rules)
```

Помимо извлечения данных свойств из модели, данные можно получить через вызов метода модели (getter).
```php

$rules = [
   'name' => [
	   'get' => 'getName', // getter $model->getName();
   ],
];

$extractor->extract($model, $rules);
```

Геттер имеит более высокий приоритет, чем свойтво prop.


### Hydrator
Класс Hydrator позволяет наполнить модель данными.


```php
$hydrator = new Hydrator;

$dto = [
    'id' => 1,
    'login' => 'Alex'
    'email' => 'some@web.dev
];

$rules = [
	'id' => [], // prop as dto`s key
	'login' => [
		'prop' => 'name', // dto key login fill property name
	],
	'email' => [
		'set' => 'setEmail', // setter $model->setEmail();
	]
];
$rules = $normalizer->normalize($rules);

$model = $hydrator->hydrate($dto, Model::class, $rules);

$model2 = new Model;
$hydrator->hydrateModel($dto, $model2, $rules);
```

Правила гидрации точно такие же как и у extractor сущности.


### HydratorService

Класс HydratorService является совмещает в себе Hydrator и Extractor.
Также он требует правил в виде сущности Rules, которая сглаживает правил и позволяет описывать их более лаконично

```php
$hydratorService = new HydratorService;
$rules = [
    'id' => [], // prop as dto`s key
    'login' => [
        'prop' => 'name', // dto key login fill property name
    ],
    'email' => [
        'set' => 'setEmail', // setter $model->setEmail();
    ]
];
$rules = $normalizer->normalize($rules);

$dto = $hydratorService->extract($model, $rules);
$model = $hydratorService->hydrate($dto, Model::class, $rules);
```

### Больше возможностей

Если требуется рекурсивная гидрация/извлечение зависимостей, требуется декларативно объявлять правила трансформации,
вызывать pipe функции для фильтрации значения, то стоит воспользоваться надсткойкой над этой билбиотекой - https://github.com/alexpts/php-data-transformer2
