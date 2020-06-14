<?php

namespace App\Repositories;

use App\Models\Escalas;
use InfyOm\Generator\Common\BaseRepository;

/**
 * Class EscalasRepository
 * @package App\Repositories
 * @version February 20, 2019, 10:07 pm UTC
 *
 * @method Escalas findWithoutFail($id, $columns = ['*'])
 * @method Escalas find($id, $columns = ['*'])
 * @method Escalas first($columns = ['*'])
*/
class EscalasRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'data',
        'linha',
        'dia_tipo',
        'planejamento',
        'numero_de_equipes',
        'carro',
        'motorista',
        'cobrador'
    ];

    /**
     * Configure the Model
     **/
    public function model()
    {
        return Escalas::class;
    }
}
