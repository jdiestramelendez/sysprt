<?php

namespace App\Repositories;

use App\Models\Dealer;
use InfyOm\Generator\Common\BaseRepository;

/**
 * Class DealerRepository
 * @package App\Repositories
 * @version November 12, 2018, 6:51 pm UTC
 *
 * @method Dealer findWithoutFail($id, $columns = ['*'])
 * @method Dealer find($id, $columns = ['*'])
 * @method Dealer first($columns = ['*'])
*/
class DealerRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'name'
    ];

    /**
     * Configure the Model
     **/
    public function model()
    {
        return Dealer::class;
    }
}
