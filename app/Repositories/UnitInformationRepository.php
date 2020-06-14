<?php

namespace App\Repositories;

use App\Models\UnitInformation;
use InfyOm\Generator\Common\BaseRepository;

/**
 * Class UnitInformationRepository
 * @package App\Repositories
 * @version November 13, 2018, 12:40 pm UTC
 *
 * @method Params findWithoutFail($id, $columns = ['*'])
 * @method Params find($id, $columns = ['*'])
 * @method Params first($columns = ['*'])
 */
class UnitInformationRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'UnitID',
        'SerialNumber'
    ];

    /**
     * Configure the Model
     **/
    public function model()
    {
        return UnitInformation::class;
    }
}
