<?php

use Faker\Factory as Faker;
use App\Models\SubGroup;
use App\Repositories\SubGroupRepository;

trait MakeSubGroupTrait
{
    /**
     * Create fake instance of SubGroup and save it in database
     *
     * @param array $subGroupFields
     * @return SubGroup
     */
    public function makeSubGroup($subGroupFields = [])
    {
        /** @var SubGroupRepository $subGroupRepo */
        $subGroupRepo = App::make(SubGroupRepository::class);
        $theme = $this->fakeSubGroupData($subGroupFields);
        return $subGroupRepo->create($theme);
    }

    /**
     * Get fake instance of SubGroup
     *
     * @param array $subGroupFields
     * @return SubGroup
     */
    public function fakeSubGroup($subGroupFields = [])
    {
        return new SubGroup($this->fakeSubGroupData($subGroupFields));
    }

    /**
     * Get fake data of SubGroup
     *
     * @param array $postFields
     * @return array
     */
    public function fakeSubGroupData($subGroupFields = [])
    {
        $fake = Faker::create();

        return array_merge([
            'name' => $fake->word,
            'group_id' => $fake->randomDigitNotNull,
            'created_at' => $fake->word,
            'updated_at' => $fake->word
        ], $subGroupFields);
    }
}
