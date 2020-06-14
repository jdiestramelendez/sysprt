<?php

namespace App\Repositories;

use App\Models\Assets;
use InfyOm\Generator\Common\BaseRepository;

/**
 * Class AssetsRepository
 * @package App\Repositories
 * @version November 13, 2018, 12:40 pm UTC
 *
 * @method Assets findWithoutFail($id, $columns = ['*'])
 * @method Assets find($id, $columns = ['*'])
 * @method Assets first($columns = ['*'])
*/
class AssetsRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'dealer_id',
        'group_id',
        'subgroup_id',
        'site_id',
        'serial_unit',
        'model',
        'year',
        'type',
        'chassi',
        'consumo',
        'capacidade',
        'passageiros',
        'notes',
        'description',
        'registration_number',
        'device',
        'status',
        'created_by'
    ];

    /**
     * Configure the Model
     **/
    public function model()
    {
        return Assets::class;
    }
}
