<?php

namespace App\Repositories;

use App\Models\Assets;
use InfyOm\Generator\Common\BaseRepository;

/**
 * Class LastTripsRepository
 * @package App\Repositories
 * @version November 13, 2018, 12:40 pm UTC
 *
 * @method Assets findWithoutFail($id, $columns = ['*'])
 * @method Assets find($id, $columns = ['*'])
 * @method Assets first($columns = ['*'])
 */
class LastTripsRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
      "TripId",
      "UnitId",
      "DriverId",
      "TripStart",
      "TripEnd",
      "StartPositionId",
      "EndPositionId",
      "Duration",
      "Distance",
      "StartOdometer",
      "EndOdometer",
      "FuelUsedLitres"
    ];

    /**
     * Configure the Model
     **/
    public function model()
    {
        return LastTrips::class;
    }
}
