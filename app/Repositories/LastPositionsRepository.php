<?php

namespace App\Repositories;

use App\Models\Assets;
use InfyOm\Generator\Common\BaseRepository;

/**
 * Class LastPositionsRepository
 * @package App\Repositories
 * @version November 13, 2018, 12:40 pm UTC
 *
 * @method Assets findWithoutFail($id, $columns = ['*'])
 * @method Assets find($id, $columns = ['*'])
 * @method Assets first($columns = ['*'])
 */
class LastPositionsRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
      "PositionId",
      "UnitId",
      "DriverId",
      "Timestamp",
      "Latitude",
      "Longitude",
      "SpeedKilometresPerHour",
      "StatusGPSAntenna",
      "Orientation",
      "NumberOfSatellites",
      "Hdop",
      "AgeOfReadingSeconds",
      "IgnitionOn",
      "OUT0",
      "Odometer",
      "IsAvl",
      "qtt"
    ];

    /**
     * Configure the Model
     **/
    public function model()
    {
        return LastPositions::class;
    }
}
