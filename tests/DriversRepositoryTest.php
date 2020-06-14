<?php

use App\Models\Drivers;
use App\Repositories\DriversRepository;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class DriversRepositoryTest extends TestCase
{
    use MakeDriversTrait, ApiTestTrait, DatabaseTransactions;

    /**
     * @var DriversRepository
     */
    protected $driversRepo;

    public function setUp()
    {
        parent::setUp();
        $this->driversRepo = App::make(DriversRepository::class);
    }

    /**
     * @test create
     */
    public function testCreateDrivers()
    {
        $drivers = $this->fakeDriversData();
        $createdDrivers = $this->driversRepo->create($drivers);
        $createdDrivers = $createdDrivers->toArray();
        $this->assertArrayHasKey('id', $createdDrivers);
        $this->assertNotNull($createdDrivers['id'], 'Created Drivers must have id specified');
        $this->assertNotNull(Drivers::find($createdDrivers['id']), 'Drivers with given id must be in DB');
        $this->assertModelData($drivers, $createdDrivers);
    }

    /**
     * @test read
     */
    public function testReadDrivers()
    {
        $drivers = $this->makeDrivers();
        $dbDrivers = $this->driversRepo->find($drivers->id);
        $dbDrivers = $dbDrivers->toArray();
        $this->assertModelData($drivers->toArray(), $dbDrivers);
    }

    /**
     * @test update
     */
    public function testUpdateDrivers()
    {
        $drivers = $this->makeDrivers();
        $fakeDrivers = $this->fakeDriversData();
        $updatedDrivers = $this->driversRepo->update($fakeDrivers, $drivers->id);
        $this->assertModelData($fakeDrivers, $updatedDrivers->toArray());
        $dbDrivers = $this->driversRepo->find($drivers->id);
        $this->assertModelData($fakeDrivers, $dbDrivers->toArray());
    }

    /**
     * @test delete
     */
    public function testDeleteDrivers()
    {
        $drivers = $this->makeDrivers();
        $resp = $this->driversRepo->delete($drivers->id);
        $this->assertTrue($resp);
        $this->assertNull(Drivers::find($drivers->id), 'Drivers should not exist in DB');
    }
}
