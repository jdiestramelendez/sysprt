<?php

use Faker\Factory as Faker;
use App\Models\QuadroHorarios;
use App\Repositories\QuadroHorariosRepository;

trait MakeQuadroHorariosTrait
{
    /**
     * Create fake instance of QuadroHorarios and save it in database
     *
     * @param array $quadroHorariosFields
     * @return QuadroHorarios
     */
    public function makeQuadroHorarios($quadroHorariosFields = [])
    {
        /** @var QuadroHorariosRepository $quadroHorariosRepo */
        $quadroHorariosRepo = App::make(QuadroHorariosRepository::class);
        $theme = $this->fakeQuadroHorariosData($quadroHorariosFields);
        return $quadroHorariosRepo->create($theme);
    }

    /**
     * Get fake instance of QuadroHorarios
     *
     * @param array $quadroHorariosFields
     * @return QuadroHorarios
     */
    public function fakeQuadroHorarios($quadroHorariosFields = [])
    {
        return new QuadroHorarios($this->fakeQuadroHorariosData($quadroHorariosFields));
    }

    /**
     * Get fake data of QuadroHorarios
     *
     * @param array $postFields
     * @return array
     */
    public function fakeQuadroHorariosData($quadroHorariosFields = [])
    {
        $fake = Faker::create();

        return array_merge([
            'linha' => $fake->word,
            'dia_tipo' => $fake->word,
            'planejamento' => $fake->word,
            'objetivo_do_quadro' => $fake->word,
            'frota' => $fake->word,
            'viagens' => $fake->word,
            'velocidade' => $fake->word,
            'codigo_de_garagem' => $fake->word,
            'sequencia_viagem' => $fake->word,
            'posicao' => $fake->word,
            'saida_da_garagem' => $fake->word,
            'chegada_no_terminal' => $fake->word,
            'ida_ou_volta' => $fake->word,
            'codigo_de_tp1' => $fake->word,
            'codigo_de_ts36' => $fake->word,
            'created_at' => $fake->word,
            'updated_at' => $fake->word
        ], $quadroHorariosFields);
    }
}
