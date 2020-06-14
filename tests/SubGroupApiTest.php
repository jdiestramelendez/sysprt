<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class SubGroupApiTest extends TestCase
{
    use MakeSubGroupTrait, ApiTestTrait, WithoutMiddleware, DatabaseTransactions;

    /**
     * @test
     */
    public function testCreateSubGroup()
    {
        $subGroup = $this->fakeSubGroupData();
        $this->json('POST', '/api/v1/subgroups', $subGroup);

        $this->assertApiResponse($subGroup);
    }

    /**
     * @test
     */
    public function testReadSubGroup()
    {
        $subGroup = $this->makeSubGroup();
        $this->json('GET', '/api/v1/subgroups/'.$subGroup->id);

        $this->assertApiResponse($subGroup->toArray());
    }

    /**
     * @test
     */
    public function testUpdateSubGroup()
    {
        $subGroup = $this->makeSubGroup();
        $editedSubGroup = $this->fakeSubGroupData();

        $this->json('PUT', '/api/v1/subgroups/'.$subGroup->id, $editedSubGroup);

        $this->assertApiResponse($editedSubGroup);
    }

    /**
     * @test
     */
    public function testDeleteSubGroup()
    {
        $subGroup = $this->makeSubGroup();
        $this->json('DELETE', '/api/v1/subgroups/'.$subGroup->id);

        $this->assertApiSuccess();
        $this->json('GET', '/api/v1/subgroups/'.$subGroup->id);

        $this->assertResponseStatus(404);
    }
}
