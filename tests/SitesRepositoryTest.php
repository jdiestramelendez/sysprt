<?php

use App\Models\Sites;
use App\Repositories\SitesRepository;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class SitesRepositoryTest extends TestCase
{
    use MakeSitesTrait, ApiTestTrait, DatabaseTransactions;

    /**
     * @var SitesRepository
     */
    protected $sitesRepo;

    public function setUp()
    {
        parent::setUp();
        $this->sitesRepo = App::make(SitesRepository::class);
    }

    /**
     * @test create
     */
    public function testCreateSites()
    {
        $sites = $this->fakeSitesData();
        $createdSites = $this->sitesRepo->create($sites);
        $createdSites = $createdSites->toArray();
        $this->assertArrayHasKey('id', $createdSites);
        $this->assertNotNull($createdSites['id'], 'Created Sites must have id specified');
        $this->assertNotNull(Sites::find($createdSites['id']), 'Sites with given id must be in DB');
        $this->assertModelData($sites, $createdSites);
    }

    /**
     * @test read
     */
    public function testReadSites()
    {
        $sites = $this->makeSites();
        $dbSites = $this->sitesRepo->find($sites->id);
        $dbSites = $dbSites->toArray();
        $this->assertModelData($sites->toArray(), $dbSites);
    }

    /**
     * @test update
     */
    public function testUpdateSites()
    {
        $sites = $this->makeSites();
        $fakeSites = $this->fakeSitesData();
        $updatedSites = $this->sitesRepo->update($fakeSites, $sites->id);
        $this->assertModelData($fakeSites, $updatedSites->toArray());
        $dbSites = $this->sitesRepo->find($sites->id);
        $this->assertModelData($fakeSites, $dbSites->toArray());
    }

    /**
     * @test delete
     */
    public function testDeleteSites()
    {
        $sites = $this->makeSites();
        $resp = $this->sitesRepo->delete($sites->id);
        $this->assertTrue($resp);
        $this->assertNull(Sites::find($sites->id), 'Sites should not exist in DB');
    }
}
