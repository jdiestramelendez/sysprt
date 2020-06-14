<?php

use App\Models\Assets;
use App\Repositories\AssetsRepository;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class AssetsRepositoryTest extends TestCase
{
    use MakeAssetsTrait, ApiTestTrait, DatabaseTransactions;

    /**
     * @var AssetsRepository
     */
    protected $assetsRepo;

    public function setUp()
    {
        parent::setUp();
        $this->assetsRepo = App::make(AssetsRepository::class);
    }

    /**
     * @test create
     */
    public function testCreateAssets()
    {
        $assets = $this->fakeAssetsData();
        $createdAssets = $this->assetsRepo->create($assets);
        $createdAssets = $createdAssets->toArray();
        $this->assertArrayHasKey('id', $createdAssets);
        $this->assertNotNull($createdAssets['id'], 'Created Assets must have id specified');
        $this->assertNotNull(Assets::find($createdAssets['id']), 'Assets with given id must be in DB');
        $this->assertModelData($assets, $createdAssets);
    }

    /**
     * @test read
     */
    public function testReadAssets()
    {
        $assets = $this->makeAssets();
        $dbAssets = $this->assetsRepo->find($assets->id);
        $dbAssets = $dbAssets->toArray();
        $this->assertModelData($assets->toArray(), $dbAssets);
    }

    /**
     * @test update
     */
    public function testUpdateAssets()
    {
        $assets = $this->makeAssets();
        $fakeAssets = $this->fakeAssetsData();
        $updatedAssets = $this->assetsRepo->update($fakeAssets, $assets->id);
        $this->assertModelData($fakeAssets, $updatedAssets->toArray());
        $dbAssets = $this->assetsRepo->find($assets->id);
        $this->assertModelData($fakeAssets, $dbAssets->toArray());
    }

    /**
     * @test delete
     */
    public function testDeleteAssets()
    {
        $assets = $this->makeAssets();
        $resp = $this->assetsRepo->delete($assets->id);
        $this->assertTrue($resp);
        $this->assertNull(Assets::find($assets->id), 'Assets should not exist in DB');
    }
}
