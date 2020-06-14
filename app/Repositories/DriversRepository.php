<?php

namespace App\Repositories;

use App\Models\Drivers;
use InfyOm\Generator\Common\BaseRepository;

/**
 * Class DriversRepository
 * @package App\Repositories
 * @version November 13, 2018, 1:09 pm UTC
 *
 * @method Drivers findWithoutFail($id, $columns = ['*'])
 * @method Drivers find($id, $columns = ['*'])
 * @method Drivers first($columns = ['*'])
*/
class DriversRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'dealer_id',
        'group_id',
        'subgroup_id',
        'site_id',
        'name',
        'cpf',
        'employee_number',
        'extended_id'
    ];

    /**
     * Configure the Model
     **/
    public function model()
    {
        return Drivers::class;
    }
}
