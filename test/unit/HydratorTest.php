<?php
declare(strict_types=1);

use Faker\Generator;
use PHPUnit\Framework\TestCase;
use PTS\Hydrator\HydrateClosure;
use PTS\Hydrator\Hydrator;
use PTS\Hydrator\HydratorException;
use PTS\Hydrator\NormalizerRule;
use PTS\Hydrator\UserModel;

require_once __DIR__ . '/data/UserModel.php';

class HydratorTest extends TestCase
{
    /** @var Hydrator */
    protected $hydrator;
    /** @var Generator */
    protected $faker;

    public function setUp(): void
    {
        $this->hydrator = new Hydrator(new HydrateClosure, new NormalizerRule);
        $this->faker = Faker\Factory::create();
    }

    protected function createUser(): array
    {
        return [
            'id' => random_int(1, 9999),
            'creAt' => new \DateTime($this->faker->date),
            'name' => $this->faker->name,
            'login' => $this->faker->name,
            'active' => $this->faker->randomElement([true, false]),
            'email' => $this->faker->email,
        ];
    }

    public function testHydrate(): void
    {
        $userDto = $this->createUser();
        $rules = [
            'id' => [],
            'creAt' => [],
            'name' => [],
            'login' => [],
            'active' => [],
            'email' => [],
        ];

        /** @var UserModel $model */
        $model = $this->hydrator->hydrate($userDto, UserModel::class, $rules);

        self::assertInstanceOf(UserModel::class, $model);
        self::assertEquals($userDto['creAt'], $model->getCreAt());
        self::assertEquals($userDto['email'], $model->getEmail());
        self::assertEquals($userDto['name'], $model->getName());
        self::assertEquals($userDto['login'], $model->getLogin());
        self::assertEquals($userDto['active'], $model->isActive());
    }

    public function testHydrateModel(): void
    {
        $userDto = $this->createUser();
        $model = new UserModel;

        $rules = [
            'id' => [],
            'creAt' => [],
            'name' => [],
            'login' => [],
            'active' => [],
            'email' => [],
        ];

        $this->hydrator->hydrateModel($userDto, $model, $rules);

        self::assertInstanceOf(UserModel::class, $model);
        self::assertEquals($userDto['creAt'], $model->getCreAt());
        self::assertEquals($userDto['email'], $model->getEmail());
        self::assertEquals($userDto['name'], $model->getName());
        self::assertEquals($userDto['login'], $model->getLogin());
        self::assertEquals($userDto['active'], $model->isActive());
    }

    public function testHydratePipe(): void
    {
        $userDto = $this->createUser();
        $userDto['creAt'] = time();
        $rules = [
            'creAt' => [
                'pipe' => [
                    function (int $value) {
                        return new \DateTime('@' . $value);
                    }
                ]
            ],
        ];

        /** @var UserModel $model */
        $model = $this->hydrator->hydrate($userDto, UserModel::class, $rules);
        self::assertInstanceOf(\DateTime::class, $model->getCreAt());
        self::assertEquals($model->getCreAt()->getTimestamp(), $userDto['creAt']);
    }

    public function testHydratePipeOnlyHydrate(): void
    {
        $userDto = $this->createUser();
        $userDto['creAt'] = time();
        $rules = [
            'creAt' => [
                'pipe' => [[
                    'hydrate' => function (int $value) {
                        return new \DateTime('@' . $value);
                    }
                ]]
            ],
        ];

        /** @var UserModel $model */
        $model = $this->hydrator->hydrate($userDto, UserModel::class, $rules);
        self::assertInstanceOf(\DateTime::class, $model->getCreAt());
        self::assertEquals($model->getCreAt()->getTimestamp(), $userDto['creAt']);
    }

    public function testExtractViaSetter(): void
    {
        $userDto = $this->createUser();
        $rules = [
            'creAt' => [
                'set' => 'setCreAt'
            ],
        ];

        /** @var UserModel $model */
        $model = $this->hydrator->hydrate($userDto, UserModel::class, $rules);
        self::assertInstanceOf(\DateTime::class, $model->getCreAt());
        self::assertEquals($userDto['creAt'], $model->getCreAt());
    }

    public function testExtractViaGettersWithParams(): void
    {
        $userDto = $this->createUser();
        $rules = [
            'name' => [
                'set' => ['setTitleName', ['Mrs.']]
            ],
        ];

        /** @var UserModel $model */
        $model = $this->hydrator->hydrate($userDto, UserModel::class, $rules);
        self::assertEquals($userDto['name'] . ' ' . 'Mrs.', $model->getName());
    }

    public function testExtractViaBadSetters(): void
    {
        $this->expectException(HydratorException::class);

        $userDto = $this->createUser();
        $rules = [
            'name' => [
                'set' => 'unknownGetter'
            ],
        ];

        $this->hydrator->hydrate($userDto, UserModel::class, $rules);
    }
}