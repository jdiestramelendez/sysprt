<?php

namespace App\Repositories;

use App\Models\QuadroHorarios;
use InfyOm\Generator\Common\BaseRepository;

/**
 * Class QuadroHorariosRepository
 * @package App\Repositories
 * @version February 21, 2019, 5:11 pm UTC
 *
 * @method QuadroHorarios findWithoutFail($id, $columns = ['*'])
 * @method QuadroHorarios find($id, $columns = ['*'])
 * @method QuadroHorarios first($columns = ['*'])
*/
class QuadroHorariosRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'linha',
        'dia_tipo',
        'planejamento',
        'objetivo_do_quadro',
        'frota',
        'viagens',
        'velocidade',
        'codigo_de_garagem',
        'sequencia_viagem',
        'posicao',
        'saida_da_garagem',
        'chegada_no_terminal',
        'ida_ou_volta',
        'codigo_de_tp1',
        'codigo_de_ts36'
    ];

    /**
     * Configure the Model
     **/
    public function model()
    {
        return QuadroHorarios::class;
    }
}
