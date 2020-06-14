<?php

use Faker\Factory as Faker;
use App\Models\Dealer;
use App\Repositories\DealerRepository;

trait MakeDealerTrait
{
    /**
     * Create fake instance of Dealer and save it in database
     *
     * @param array $dealerFields
     * @return Dealer
     */
    public function makeDealer($dealerFields = [])
    {
        /** @var DealerRepository $dealerRepo */
        $dealerRepo = App::make(DealerRepository::class);
        $theme = $this->fakeDealerData($dealerFields);
        return $dealerRepo->create($theme);
    }

    /**
     * Get fake instance of Dealer
     *
     * @param array $dealerFields
     * @return Dealer
     */
    public function fakeDealer($dealerFields = [])
    {
        return new Dealer($this->fakeDealerData($dealerFields));
    }

    /**
     * Get fake data of Dealer
     *
     * @param array $postFields
     * @return array
     */
    public function fakeDealerData($dealerFields = [])
    {
        $fake = Faker::create();

        return array_merge([
            'id' => $fake->word,
            'name' => $fake->word,
            'created_at' => $fake->word,
            'updated_at' => $fake->word
        ], $dealerFields);
    }
}
