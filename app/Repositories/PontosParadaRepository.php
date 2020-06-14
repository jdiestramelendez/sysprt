<?php

namespace App\Repositories;

use App\Models\PontosParada;
use InfyOm\Generator\Common\BaseRepository;

/**
 * Class PontosParadaRepository
 * @package App\Repositories
 * @version November 13, 2018, 11:23 am UTC
 *
 * @method PontosParada findWithoutFail($id, $columns = ['*'])
 * @method PontosParada find($id, $columns = ['*'])
 * @method PontosParada first($columns = ['*'])
*/
class PontosParadaRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
      'endereco',
      'tipo',
      'codigo_referencia',
      'cerca',
      'lat',
      'lng',
      'nome'
    ];

    /**
     * Configure the Model
     **/
    public function model()
    {
        return PontosParada::class;
    }
}
