<?php

use App\Models\QuadroHorarios;
use App\Repositories\QuadroHorariosRepository;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class QuadroHorariosRepositoryTest extends TestCase
{
    use MakeQuadroHorariosTrait, ApiTestTrait, DatabaseTransactions;

    /**
     * @var QuadroHorariosRepository
     */
    protected $quadroHorariosRepo;

    public function setUp()
    {
        parent::setUp();
        $this->quadroHorariosRepo = App::make(QuadroHorariosRepository::class);
    }

    /**
     * @test create
     */
    public function testCreateQuadroHorarios()
    {
        $quadroHorarios = $this->fakeQuadroHorariosData();
        $createdQuadroHorarios = $this->quadroHorariosRepo->create($quadroHorarios);
        $createdQuadroHorarios = $createdQuadroHorarios->toArray();
        $this->assertArrayHasKey('id', $createdQuadroHorarios);
        $this->assertNotNull($createdQuadroHorarios['id'], 'Created QuadroHorarios must have id specified');
        $this->assertNotNull(QuadroHorarios::find($createdQuadroHorarios['id']), 'QuadroHorarios with given id must be in DB');
        $this->assertModelData($quadroHorarios, $createdQuadroHorarios);
    }

    /**
     * @test read
     */
    public function testReadQuadroHorarios()
    {
        $quadroHorarios = $this->makeQuadroHorarios();
        $dbQuadroHorarios = $this->quadroHorariosRepo->find($quadroHorarios->id);
        $dbQuadroHorarios = $dbQuadroHorarios->toArray();
        $this->assertModelData($quadroHorarios->toArray(), $dbQuadroHorarios);
    }

    /**
     * @test update
     */
    public function testUpdateQuadroHorarios()
    {
        $quadroHorarios = $this->makeQuadroHorarios();
        $fakeQuadroHorarios = $this->fakeQuadroHorariosData();
        $updatedQuadroHorarios = $this->quadroHorariosRepo->update($fakeQuadroHorarios, $quadroHorarios->id);
        $this->assertModelData($fakeQuadroHorarios, $updatedQuadroHorarios->toArray());
        $dbQuadroHorarios = $this->quadroHorariosRepo->find($quadroHorarios->id);
        $this->assertModelData($fakeQuadroHorarios, $dbQuadroHorarios->toArray());
    }

    /**
     * @test delete
     */
    public function testDeleteQuadroHorarios()
    {
        $quadroHorarios = $this->makeQuadroHorarios();
        $resp = $this->quadroHorariosRepo->delete($quadroHorarios->id);
        $this->assertTrue($resp);
        $this->assertNull(QuadroHorarios::find($quadroHorarios->id), 'QuadroHorarios should not exist in DB');
    }
}
