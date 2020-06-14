<?php

namespace App\Repositories;

use App\Models\Sites;
use InfyOm\Generator\Common\BaseRepository;

/**
 * Class SitesRepository
 * @package App\Repositories
 * @version November 13, 2018, 11:58 am UTC
 *
 * @method Sites findWithoutFail($id, $columns = ['*'])
 * @method Sites find($id, $columns = ['*'])
 * @method Sites first($columns = ['*'])
*/
class SitesRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'name',
        'notes',
        'subgroup_id'
    ];

    /**
     * Configure the Model
     **/
    public function model()
    {
        return Sites::class;
    }
}
