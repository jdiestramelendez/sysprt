<?php

namespace App\Repositories;

use App\Models\EventsLibrary;
use InfyOm\Generator\Common\BaseRepository;

/**
 * Class EventsLibraryRepository
 * @package App\Repositories
 * @version November 12, 2018, 7:08 pm UTC
 *
 * @method Group findWithoutFail($id, $columns = ['*'])
 * @method Group find($id, $columns = ['*'])
 * @method Group first($columns = ['*'])
 */
class EventsLibraryRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'name',
        'dealer_id'
    ];

    /**
     * Configure the Model
     **/
    public function model()
    {
        return EventsLibrary::class;
    }
}
