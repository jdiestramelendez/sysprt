<?php

namespace App\Repositories;

use App\Models\Params;
use InfyOm\Generator\Common\BaseRepository;

/**
 * Class ParamsRepository
 * @package App\Repositories
 * @version November 13, 2018, 12:40 pm UTC
 *
 * @method Params findWithoutFail($id, $columns = ['*'])
 * @method Params find($id, $columns = ['*'])
 * @method Params first($columns = ['*'])
 */
class ParamsRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'fuso',
        'pos_low_time',
        'pos_high_time',
    ];

    /**
     * Configure the Model
     **/
    public function model()
    {
        return Params::class;
    }
}
