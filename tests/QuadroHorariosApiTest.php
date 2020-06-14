<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class QuadroHorariosApiTest extends TestCase
{
    use MakeQuadroHorariosTrait, ApiTestTrait, WithoutMiddleware, DatabaseTransactions;

    /**
     * @test
     */
    public function testCreateQuadroHorarios()
    {
        $quadroHorarios = $this->fakeQuadroHorariosData();
        $this->json('POST', '/api/v1/quadroHorarios', $quadroHorarios);

        $this->assertApiResponse($quadroHorarios);
    }

    /**
     * @test
     */
    public function testReadQuadroHorarios()
    {
        $quadroHorarios = $this->makeQuadroHorarios();
        $this->json('GET', '/api/v1/quadroHorarios/'.$quadroHorarios->id);

        $this->assertApiResponse($quadroHorarios->toArray());
    }

    /**
     * @test
     */
    public function testUpdateQuadroHorarios()
    {
        $quadroHorarios = $this->makeQuadroHorarios();
        $editedQuadroHorarios = $this->fakeQuadroHorariosData();

        $this->json('PUT', '/api/v1/quadroHorarios/'.$quadroHorarios->id, $editedQuadroHorarios);

        $this->assertApiResponse($editedQuadroHorarios);
    }

    /**
     * @test
     */
    public function testDeleteQuadroHorarios()
    {
        $quadroHorarios = $this->makeQuadroHorarios();
        $this->json('DELETE', '/api/v1/quadroHorarios/'.$quadroHorarios->id);

        $this->assertApiSuccess();
        $this->json('GET', '/api/v1/quadroHorarios/'.$quadroHorarios->id);

        $this->assertResponseStatus(404);
    }
}
