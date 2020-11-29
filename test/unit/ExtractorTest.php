<?php
declare(strict_types=1);

use Faker\Generator;
use PHPUnit\Framework\TestCase;
use PTS\Hydrator\ExtractClosure;
use PTS\Hydrator\Extractor;
use PTS\Hydrator\Normalizer;
use PTS\Hydrator\UserModel;

require_once __DIR__ . '/data/UserModel.php';

class ExtractorTest extends TestCase
{
    protected Extractor $hydrator;
    protected Generator $faker;
    protected Normalizer $normalizer;

    public function setUp(): void
    {
        $this->hydrator = new Extractor(new ExtractClosure);
        $this->faker = Faker\Factory::create();
        $this->normalizer = new Normalizer;
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
        $rules = $this->normalizer->normalize($rules);

        $dto = $this->hydrator->extract($user, $rules);

        self::assertCount(6, $dto);
        self::assertInstanceOf('DateTime', $dto['creAt']);
        self::assertEquals($user->getEmail(), $dto['email']);
        self::assertEquals($user->getName(), $dto['name']);
        self::assertEquals($user->getLogin(), $dto['login']);
        self::assertEquals($user->isActive(), $dto['active']);
    }

    public function testExtractViaGetters(): void
    {
        $user = $this->createUser();
        $rules = [
            'creAt' => [
                'get' => 'getCreAtTimestamp'
            ],
        ];

        $dto = $this->hydrator->extract($user, $rules);
        self::assertEquals($user->getCreAt()->getTimestamp(), $dto['creAt']);
        self::assertIsInt($dto['creAt']);
    }

    public function testExtractViaGettersWithParams(): void
    {
        $user = $this->createUser();
        $rules = [
            'titleName' => [
                'get' => ['getTitleName', ['Mrs.']]
            ],
        ];

        $dto = $this->hydrator->extract($user, $rules);
        self::assertEquals($user->getTitleName('Mrs.'), $dto['titleName']);
    }

    public function testExtractViaBadGetters(): void
    {
        $this->expectException(TypeError::class);

        $user = $this->createUser();
        $rules = [
            'titleName' => [
                'get' => 'unknownGetter'
            ],
        ];

        $this->hydrator->extract($user, $rules);
    }
}
