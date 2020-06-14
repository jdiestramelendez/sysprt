<?php

namespace App\Repositories;

use App\Models\Dashboards;
use InfyOm\Generator\Common\BaseRepository;

/**
 * Class DashboardsRepository
 * @package App\Repositories
 * @version November 21, 2018, 11:46 am UTC
 *
 * @method Dashboards findWithoutFail($id, $columns = ['*'])
 * @method Dashboards find($id, $columns = ['*'])
 * @method Dashboards first($columns = ['*'])
*/
class DashboardsRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'name',
        'url'
    ];

    /**
     * Configure the Model
     **/
    public function model()
    {
        return Dashboards::class;
    }
}
