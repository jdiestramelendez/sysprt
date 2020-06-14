<?php

namespace App\Repositories;

use App\Models\Itinerarios;
use InfyOm\Generator\Common\BaseRepository;

/**
 * Class ItinerariosRepository
 * @package App\Repositories
 * @version November 13, 2018, 11:23 am UTC
 *
 * @method Itinerarios findWithoutFail($id, $columns = ['*'])
 * @method Itinerarios find($id, $columns = ['*'])
 * @method Itinerarios first($columns = ['*'])
*/
class ItinerariosRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
      'linha_id',
      'sentido',
      'pontos_parada_ids',
      'pontos_inicial_id',
      'pontos_final_id',
    ];

    /**
     * Configure the Model
     **/
    public function model()
    {
        return Itinerarios::class;
    }
}
