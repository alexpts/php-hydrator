<?php
declare(strict_types=1);

use Faker\Generator;
use PHPUnit\Framework\TestCase;
use PTS\Hydrator\ExtractClosure;
use PTS\Hydrator\Extractor;
use PTS\Hydrator\HydrateClosure;
use PTS\Hydrator\Hydrator;
use PTS\Hydrator\HydratorService;
use PTS\Hydrator\NormalizerRule;
use PTS\Hydrator\UserModel;

require_once __DIR__ . '/data/UserModel.php';

class HydratorServiceTest extends TestCase
{
    /** @var HydratorService */
    protected $hydrator;
    /** @var Generator */
    protected $faker;

    public function setUp(): void
    {
        $hydrator = new Hydrator(new HydrateClosure, new NormalizerRule);
        $extractor = new Extractor(new ExtractClosure, new NormalizerRule);

        $this->hydrator = new HydratorService($extractor, $hydrator);
        $this->faker = Faker\Factory::create();
    }

    protected function createUserDto(): array
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
        $userDto = $this->createUserDto();
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
        $userDto = $this->createUserDto();
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

    protected function createUser(): UserModel
    {
        $user = new UserModel;
        $user->setActive($this->faker->randomElement([true, false]));
        $user->setEmail($this->faker->email);
        $user->setLogin($this->faker->name);
        $user->setName($this->faker->name);

        return $user;
    }

    public function testExtract(): void
    {
        $user = $this->createUser();
        $rules = [
            'id' => [],
            'creAt' => [],
            'name' => [],
            'login' => [],
            'active' => [],
            'email' => [],
        ];

        $dto = $this->hydrator->extract($user, $rules);

        self::assertCount(5, $dto);
        self::assertInstanceOf('DateTime', $dto['creAt']);
        self::assertEquals($user->getEmail(), $dto['email']);
        self::assertEquals($user->getName(), $dto['name']);
        self::assertEquals($user->getLogin(), $dto['login']);
        self::assertEquals($user->isActive(), $dto['active']);
    }
}