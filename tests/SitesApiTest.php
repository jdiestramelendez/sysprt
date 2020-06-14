<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class SitesApiTest extends TestCase
{
    use MakeSitesTrait, ApiTestTrait, WithoutMiddleware, DatabaseTransactions;

    /**
     * @test
     */
    public function testCreateSites()
    {
        $sites = $this->fakeSitesData();
        $this->json('POST', '/api/v1/sites', $sites);

        $this->assertApiResponse($sites);
    }

    /**
     * @test
     */
    public function testReadSites()
    {
        $sites = $this->makeSites();
        $this->json('GET', '/api/v1/sites/'.$sites->id);

        $this->assertApiResponse($sites->toArray());
    }

    /**
     * @test
     */
    public function testUpdateSites()
    {
        $sites = $this->makeSites();
        $editedSites = $this->fakeSitesData();

        $this->json('PUT', '/api/v1/sites/'.$sites->id, $editedSites);

        $this->assertApiResponse($editedSites);
    }

    /**
     * @test
     */
    public function testDeleteSites()
    {
        $sites = $this->makeSites();
        $this->json('DELETE', '/api/v1/sites/'.$sites->id);

        $this->assertApiSuccess();
        $this->json('GET', '/api/v1/sites/'.$sites->id);

        $this->assertResponseStatus(404);
    }
}
