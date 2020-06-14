<?php

use Faker\Factory as Faker;
use App\Models\Escalas;
use App\Repositories\EscalasRepository;

trait MakeEscalasTrait
{
    /**
     * Create fake instance of Escalas and save it in database
     *
     * @param array $escalasFields
     * @return Escalas
     */
    public function makeEscalas($escalasFields = [])
    {
        /** @var EscalasRepository $escalasRepo */
        $escalasRepo = App::make(EscalasRepository::class);
        $theme = $this->fakeEscalasData($escalasFields);
        return $escalasRepo->create($theme);
    }

    /**
     * Get fake instance of Escalas
     *
     * @param array $escalasFields
     * @return Escalas
     */
    public function fakeEscalas($escalasFields = [])
    {
        return new Escalas($this->fakeEscalasData($escalasFields));
    }

    /**
     * Get fake data of Escalas
     *
     * @param array $postFields
     * @return array
     */
    public function fakeEscalasData($escalasFields = [])
    {
        $fake = Faker::create();

        return array_merge([
            'data' => $fake->word,
            'linha' => $fake->word,
            'dia_tipo' => $fake->word,
            'planejamento' => $fake->word,
            'numero_de_equipes' => $fake->word,
            'carro' => $fake->word,
            'motorista' => $fake->word,
            'cobrador' => $fake->word,
            'created_at' => $fake->word,
            'updated_at' => $fake->word
        ], $escalasFields);
    }
}
