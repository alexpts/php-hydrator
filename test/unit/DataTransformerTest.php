<?php
declare(strict_types=1);

use PHPUnit\Framework\TestCase;
use PTS\Hydrator\DataTransformer;
use PTS\Hydrator\ExtractClosure;
use PTS\Hydrator\Extractor;
use PTS\Hydrator\HydrateClosure;
use PTS\Hydrator\Hydrator;
use PTS\Hydrator\HydratorService;
use PTS\Hydrator\MapsManager;
use PTS\Hydrator\NormalizerRule;
use PTS\Hydrator\UserModel;

class DataTransformerTest extends TestCase
{
    /** @var DataTransformer */
    protected $dataTransformer;

    public function setUp(): void
    {
        $normalizeRule = new NormalizerRule;
        $extractor = new Extractor(new ExtractClosure, $normalizeRule);
        $hydrator = new Hydrator(new HydrateClosure, $normalizeRule);
        $hydratorService = new HydratorService($extractor, $hydrator);

        $mapsManager = new MapsManager;
        $mapsManager->setMapDir(UserModel::class, __DIR__ . '/data');

        $this->dataTransformer = new DataTransformer($hydratorService, $mapsManager);
    }

    public function testConstructor(): void
    {
        $this->assertInstanceOf(DataTransformer::class, $this->dataTransformer);
    }

    public function testGetMapsManager(): void
    {
        $this->assertInstanceOf(MapsManager::class, $this->dataTransformer->getMapsManager());
    }

    public function testToModel(): void
    {
        /** @var UserModel $model */
        $model = $this->dataTransformer->toModel([
            'id' => 1,
            'creAt' => new \DateTime,
            'name' => 'Alex',
            'active' => 1,
        ], UserModel::class);

        $this->assertInstanceOf(UserModel::class, $model);
        $this->assertEquals(true, $model->isActive());
        $this->assertEquals(1, $model->getId());
        $this->assertEquals('Alex', $model->getName());
    }

    public function testFillModel(): void
    {
        $model = new UserModel;
        $this->dataTransformer->fillModel([
            'id' => 1,
            'creAt' => new \DateTime,
            'name' => 'Alex',
            'active' => 1,
        ], $model);

        $this->assertInstanceOf(UserModel::class, $model);
        $this->assertEquals(true, $model->isActive());
        $this->assertEquals(1, $model->getId());
        $this->assertEquals('Alex', $model->getName());
    }

    public function testToDTO(): void
    {
        $model = new UserModel;
        $model->setId(1);
        $model->setActive(true);
        $model->setEmail('some@web.dev');

        $dto = $this->dataTransformer->toDTO($model);
        $this->assertEquals([
            'id' => 1,
            'creAt' => $model->getCreAt(),
            'name' => null,
            'login' => null,
            'active' => true,
            'email' => 'some@web.dev',
        ], $dto);
    }

    public function testToDTOWithExcludeFields(): void
    {
        $model = new UserModel;
        $model->setId(1);
        $model->setActive(false);

        $dto = $this->dataTransformer->toDTO($model, 'dto', ['email', 'login']);
        $this->assertEquals([
            'id' => 1,
            'creAt' => $model->getCreAt(),
            'name' => null,
            'active' => false,
        ], $dto);
    }
}