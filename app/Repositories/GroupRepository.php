<?php

namespace App\Repositories;

use App\Models\Group;
use InfyOm\Generator\Common\BaseRepository;

/**
 * Class GroupRepository
 * @package App\Repositories
 * @version November 12, 2018, 7:08 pm UTC
 *
 * @method Group findWithoutFail($id, $columns = ['*'])
 * @method Group find($id, $columns = ['*'])
 * @method Group first($columns = ['*'])
*/
class GroupRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'name',
        'dealer_id',
        'host',
        'database_name',
        'username',
        'password'
    ];

    /**
     * Configure the Model
     **/
    public function model()
    {
        return Group::class;
    }
}
