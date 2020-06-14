<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class DriversApiTest extends TestCase
{
    use MakeDriversTrait, ApiTestTrait, WithoutMiddleware, DatabaseTransactions;

    /**
     * @test
     */
    public function testCreateDrivers()
    {
        $drivers = $this->fakeDriversData();
        $this->json('POST', '/api/v1/drivers', $drivers);

        $this->assertApiResponse($drivers);
    }

    /**
     * @test
     */
    public function testReadDrivers()
    {
        $drivers = $this->makeDrivers();
        $this->json('GET', '/api/v1/drivers/'.$drivers->id);

        $this->assertApiResponse($drivers->toArray());
    }

    /**
     * @test
     */
    public function testUpdateDrivers()
    {
        $drivers = $this->makeDrivers();
        $editedDrivers = $this->fakeDriversData();

        $this->json('PUT', '/api/v1/drivers/'.$drivers->id, $editedDrivers);

        $this->assertApiResponse($editedDrivers);
    }

    /**
     * @test
     */
    public function testDeleteDrivers()
    {
        $drivers = $this->makeDrivers();
        $this->json('DELETE', '/api/v1/drivers/'.$drivers->id);

        $this->assertApiSuccess();
        $this->json('GET', '/api/v1/drivers/'.$drivers->id);

        $this->assertResponseStatus(404);
    }
}
