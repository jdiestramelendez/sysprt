<?php

namespace App\Repositories;

use App\Models\SubGroup;
use InfyOm\Generator\Common\BaseRepository;

/**
 * Class SubGroupRepository
 * @package App\Repositories
 * @version November 13, 2018, 11:23 am UTC
 *
 * @method SubGroup findWithoutFail($id, $columns = ['*'])
 * @method SubGroup find($id, $columns = ['*'])
 * @method SubGroup first($columns = ['*'])
*/
class SubGroupRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'name',
        'group_id'
    ];

    /**
     * Configure the Model
     **/
    public function model()
    {
        return SubGroup::class;
    }
}
