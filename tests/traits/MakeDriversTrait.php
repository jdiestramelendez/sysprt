<?php

use Faker\Factory as Faker;
use App\Models\Drivers;
use App\Repositories\DriversRepository;

trait MakeDriversTrait
{
    /**
     * Create fake instance of Drivers and save it in database
     *
     * @param array $driversFields
     * @return Drivers
     */
    public function makeDrivers($driversFields = [])
    {
        /** @var DriversRepository $driversRepo */
        $driversRepo = App::make(DriversRepository::class);
        $theme = $this->fakeDriversData($driversFields);
        return $driversRepo->create($theme);
    }

    /**
     * Get fake instance of Drivers
     *
     * @param array $driversFields
     * @return Drivers
     */
    public function fakeDrivers($driversFields = [])
    {
        return new Drivers($this->fakeDriversData($driversFields));
    }

    /**
     * Get fake data of Drivers
     *
     * @param array $postFields
     * @return array
     */
    public function fakeDriversData($driversFields = [])
    {
        $fake = Faker::create();

        return array_merge([
            'drivers_id' => $fake->randomDigitNotNull,
            'dealer_id' => $fake->randomDigitNotNull,
            'group_id' => $fake->randomDigitNotNull,
            'subgroup_id' => $fake->randomDigitNotNull,
            'site_id' => $fake->randomDigitNotNull,
            'name' => $fake->word,
            'employee_number' => $fake->word,
            'extended_id' => $fake->word,
            'created_by' => $fake->randomDigitNotNull,
            'created_at' => $fake->word,
            'updated_at' => $fake->word
        ], $driversFields);
    }
}
