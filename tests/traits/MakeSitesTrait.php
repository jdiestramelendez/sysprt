<?php

use Faker\Factory as Faker;
use App\Models\Sites;
use App\Repositories\SitesRepository;

trait MakeSitesTrait
{
    /**
     * Create fake instance of Sites and save it in database
     *
     * @param array $sitesFields
     * @return Sites
     */
    public function makeSites($sitesFields = [])
    {
        /** @var SitesRepository $sitesRepo */
        $sitesRepo = App::make(SitesRepository::class);
        $theme = $this->fakeSitesData($sitesFields);
        return $sitesRepo->create($theme);
    }

    /**
     * Get fake instance of Sites
     *
     * @param array $sitesFields
     * @return Sites
     */
    public function fakeSites($sitesFields = [])
    {
        return new Sites($this->fakeSitesData($sitesFields));
    }

    /**
     * Get fake data of Sites
     *
     * @param array $postFields
     * @return array
     */
    public function fakeSitesData($sitesFields = [])
    {
        $fake = Faker::create();

        return array_merge([
            'name' => $fake->word,
            'notes' => $fake->word,
            'subgroup_id' => $fake->randomDigitNotNull,
            'created_at' => $fake->word,
            'updated_at' => $fake->word
        ], $sitesFields);
    }
}
