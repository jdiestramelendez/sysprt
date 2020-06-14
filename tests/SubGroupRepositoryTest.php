<?php

use App\Models\SubGroup;
use App\Repositories\SubGroupRepository;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class SubGroupRepositoryTest extends TestCase
{
    use MakeSubGroupTrait, ApiTestTrait, DatabaseTransactions;

    /**
     * @var SubGroupRepository
     */
    protected $subGroupRepo;

    public function setUp()
    {
        parent::setUp();
        $this->subGroupRepo = App::make(SubGroupRepository::class);
    }

    /**
     * @test create
     */
    public function testCreateSubGroup()
    {
        $subGroup = $this->fakeSubGroupData();
        $createdSubGroup = $this->subGroupRepo->create($subGroup);
        $createdSubGroup = $createdSubGroup->toArray();
        $this->assertArrayHasKey('id', $createdSubGroup);
        $this->assertNotNull($createdSubGroup['id'], 'Created SubGroup must have id specified');
        $this->assertNotNull(SubGroup::find($createdSubGroup['id']), 'SubGroup with given id must be in DB');
        $this->assertModelData($subGroup, $createdSubGroup);
    }

    /**
     * @test read
     */
    public function testReadSubGroup()
    {
        $subGroup = $this->makeSubGroup();
        $dbSubGroup = $this->subGroupRepo->find($subGroup->id);
        $dbSubGroup = $dbSubGroup->toArray();
        $this->assertModelData($subGroup->toArray(), $dbSubGroup);
    }

    /**
     * @test update
     */
    public function testUpdateSubGroup()
    {
        $subGroup = $this->makeSubGroup();
        $fakeSubGroup = $this->fakeSubGroupData();
        $updatedSubGroup = $this->subGroupRepo->update($fakeSubGroup, $subGroup->id);
        $this->assertModelData($fakeSubGroup, $updatedSubGroup->toArray());
        $dbSubGroup = $this->subGroupRepo->find($subGroup->id);
        $this->assertModelData($fakeSubGroup, $dbSubGroup->toArray());
    }

    /**
     * @test delete
     */
    public function testDeleteSubGroup()
    {
        $subGroup = $this->makeSubGroup();
        $resp = $this->subGroupRepo->delete($subGroup->id);
        $this->assertTrue($resp);
        $this->assertNull(SubGroup::find($subGroup->id), 'SubGroup should not exist in DB');
    }
}
