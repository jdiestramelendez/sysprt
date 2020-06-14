<?php

use Faker\Factory as Faker;
use App\Models\Assets;
use App\Repositories\AssetsRepository;

trait MakeAssetsTrait
{
    /**
     * Create fake instance of Assets and save it in database
     *
     * @param array $assetsFields
     * @return Assets
     */
    public function makeAssets($assetsFields = [])
    {
        /** @var AssetsRepository $assetsRepo */
        $assetsRepo = App::make(AssetsRepository::class);
        $theme = $this->fakeAssetsData($assetsFields);
        return $assetsRepo->create($theme);
    }

    /**
     * Get fake instance of Assets
     *
     * @param array $assetsFields
     * @return Assets
     */
    public function fakeAssets($assetsFields = [])
    {
        return new Assets($this->fakeAssetsData($assetsFields));
    }

    /**
     * Get fake data of Assets
     *
     * @param array $postFields
     * @return array
     */
    public function fakeAssetsData($assetsFields = [])
    {
        $fake = Faker::create();

        return array_merge([
            'assets_id' => $fake->randomDigitNotNull,
            'dealer_id' => $fake->randomDigitNotNull,
            'group_id' => $fake->randomDigitNotNull,
            'subgroup_id' => $fake->randomDigitNotNull,
            'site_id' => $fake->randomDigitNotNull,
            'serial_unit' => $fake->word,
            'description' => $fake->word,
            'registration_number' => $fake->word,
            'device' => $fake->word,
            'status' => $fake->word,
            'created_by' => $fake->randomDigitNotNull,
            'created_at' => $fake->word,
            'updated_at' => $fake->word
        ], $assetsFields);
    }
}
