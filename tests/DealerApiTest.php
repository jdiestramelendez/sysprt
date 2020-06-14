<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class DealerApiTest extends TestCase
{
    use MakeDealerTrait, ApiTestTrait, WithoutMiddleware, DatabaseTransactions;

    /**
     * @test
     */
    public function testCreateDealer()
    {
        $dealer = $this->fakeDealerData();
        $this->json('POST', '/api/v1/dealers', $dealer);

        $this->assertApiResponse($dealer);
    }

    /**
     * @test
     */
    public function testReadDealer()
    {
        $dealer = $this->makeDealer();
        $this->json('GET', '/api/v1/dealers/'.$dealer->id);

        $this->assertApiResponse($dealer->toArray());
    }

    /**
     * @test
     */
    public function testUpdateDealer()
    {
        $dealer = $this->makeDealer();
        $editedDealer = $this->fakeDealerData();

        $this->json('PUT', '/api/v1/dealers/'.$dealer->id, $editedDealer);

        $this->assertApiResponse($editedDealer);
    }

    /**
     * @test
     */
    public function testDeleteDealer()
    {
        $dealer = $this->makeDealer();
        $this->json('DELETE', '/api/v1/dealers/'.$dealer->id);

        $this->assertApiSuccess();
        $this->json('GET', '/api/v1/dealers/'.$dealer->id);

        $this->assertResponseStatus(404);
    }
}
