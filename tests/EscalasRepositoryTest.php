<?php

use App\Models\Escalas;
use App\Repositories\EscalasRepository;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class EscalasRepositoryTest extends TestCase
{
    use MakeEscalasTrait, ApiTestTrait, DatabaseTransactions;

    /**
     * @var EscalasRepository
     */
    protected $escalasRepo;

    public function setUp()
    {
        parent::setUp();
        $this->escalasRepo = App::make(EscalasRepository::class);
    }

    /**
     * @test create
     */
    public function testCreateEscalas()
    {
        $escalas = $this->fakeEscalasData();
        $createdEscalas = $this->escalasRepo->create($escalas);
        $createdEscalas = $createdEscalas->toArray();
        $this->assertArrayHasKey('id', $createdEscalas);
        $this->assertNotNull($createdEscalas['id'], 'Created Escalas must have id specified');
        $this->assertNotNull(Escalas::find($createdEscalas['id']), 'Escalas with given id must be in DB');
        $this->assertModelData($escalas, $createdEscalas);
    }

    /**
     * @test read
     */
    public function testReadEscalas()
    {
        $escalas = $this->makeEscalas();
        $dbEscalas = $this->escalasRepo->find($escalas->id);
        $dbEscalas = $dbEscalas->toArray();
        $this->assertModelData($escalas->toArray(), $dbEscalas);
    }

    /**
     * @test update
     */
    public function testUpdateEscalas()
    {
        $escalas = $this->makeEscalas();
        $fakeEscalas = $this->fakeEscalasData();
        $updatedEscalas = $this->escalasRepo->update($fakeEscalas, $escalas->id);
        $this->assertModelData($fakeEscalas, $updatedEscalas->toArray());
        $dbEscalas = $this->escalasRepo->find($escalas->id);
        $this->assertModelData($fakeEscalas, $dbEscalas->toArray());
    }

    /**
     * @test delete
     */
    public function testDeleteEscalas()
    {
        $escalas = $this->makeEscalas();
        $resp = $this->escalasRepo->delete($escalas->id);
        $this->assertTrue($resp);
        $this->assertNull(Escalas::find($escalas->id), 'Escalas should not exist in DB');
    }
}
