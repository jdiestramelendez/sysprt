<?php

use App\Models\Dealer;
use App\Repositories\DealerRepository;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class DealerRepositoryTest extends TestCase
{
    use MakeDealerTrait, ApiTestTrait, DatabaseTransactions;

    /**
     * @var DealerRepository
     */
    protected $dealerRepo;

    public function setUp()
    {
        parent::setUp();
        $this->dealerRepo = App::make(DealerRepository::class);
    }

    /**
     * @test create
     */
    public function testCreateDealer()
    {
        $dealer = $this->fakeDealerData();
        $createdDealer = $this->dealerRepo->create($dealer);
        $createdDealer = $createdDealer->toArray();
        $this->assertArrayHasKey('id', $createdDealer);
        $this->assertNotNull($createdDealer['id'], 'Created Dealer must have id specified');
        $this->assertNotNull(Dealer::find($createdDealer['id']), 'Dealer with given id must be in DB');
        $this->assertModelData($dealer, $createdDealer);
    }

    /**
     * @test read
     */
    public function testReadDealer()
    {
        $dealer = $this->makeDealer();
        $dbDealer = $this->dealerRepo->find($dealer->id);
        $dbDealer = $dbDealer->toArray();
        $this->assertModelData($dealer->toArray(), $dbDealer);
    }

    /**
     * @test update
     */
    public function testUpdateDealer()
    {
        $dealer = $this->makeDealer();
        $fakeDealer = $this->fakeDealerData();
        $updatedDealer = $this->dealerRepo->update($fakeDealer, $dealer->id);
        $this->assertModelData($fakeDealer, $updatedDealer->toArray());
        $dbDealer = $this->dealerRepo->find($dealer->id);
        $this->assertModelData($fakeDealer, $dbDealer->toArray());
    }

    /**
     * @test delete
     */
    public function testDeleteDealer()
    {
        $dealer = $this->makeDealer();
        $resp = $this->dealerRepo->delete($dealer->id);
        $this->assertTrue($resp);
        $this->assertNull(Dealer::find($dealer->id), 'Dealer should not exist in DB');
    }
}
