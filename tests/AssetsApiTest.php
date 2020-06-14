<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class AssetsApiTest extends TestCase
{
    use MakeAssetsTrait, ApiTestTrait, WithoutMiddleware, DatabaseTransactions;

    /**
     * @test
     */
    public function testCreateAssets()
    {
        $assets = $this->fakeAssetsData();
        $this->json('POST', '/api/v1/assets', $assets);

        $this->assertApiResponse($assets);
    }

    /**
     * @test
     */
    public function testReadAssets()
    {
        $assets = $this->makeAssets();
        $this->json('GET', '/api/v1/assets/'.$assets->id);

        $this->assertApiResponse($assets->toArray());
    }

    /**
     * @test
     */
    public function testUpdateAssets()
    {
        $assets = $this->makeAssets();
        $editedAssets = $this->fakeAssetsData();

        $this->json('PUT', '/api/v1/assets/'.$assets->id, $editedAssets);

        $this->assertApiResponse($editedAssets);
    }

    /**
     * @test
     */
    public function testDeleteAssets()
    {
        $assets = $this->makeAssets();
        $this->json('DELETE', '/api/v1/assets/'.$assets->id);

        $this->assertApiSuccess();
        $this->json('GET', '/api/v1/assets/'.$assets->id);

        $this->assertResponseStatus(404);
    }
}
