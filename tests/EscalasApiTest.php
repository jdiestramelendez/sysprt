<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class EscalasApiTest extends TestCase
{
    use MakeEscalasTrait, ApiTestTrait, WithoutMiddleware, DatabaseTransactions;

    /**
     * @test
     */
    public function testCreateEscalas()
    {
        $escalas = $this->fakeEscalasData();
        $this->json('POST', '/api/v1/escalas', $escalas);

        $this->assertApiResponse($escalas);
    }

    /**
     * @test
     */
    public function testReadEscalas()
    {
        $escalas = $this->makeEscalas();
        $this->json('GET', '/api/v1/escalas/'.$escalas->id);

        $this->assertApiResponse($escalas->toArray());
    }

    /**
     * @test
     */
    public function testUpdateEscalas()
    {
        $escalas = $this->makeEscalas();
        $editedEscalas = $this->fakeEscalasData();

        $this->json('PUT', '/api/v1/escalas/'.$escalas->id, $editedEscalas);

        $this->assertApiResponse($editedEscalas);
    }

    /**
     * @test
     */
    public function testDeleteEscalas()
    {
        $escalas = $this->makeEscalas();
        $this->json('DELETE', '/api/v1/escalas/'.$escalas->id);

        $this->assertApiSuccess();
        $this->json('GET', '/api/v1/escalas/'.$escalas->id);

        $this->assertResponseStatus(404);
    }
}
