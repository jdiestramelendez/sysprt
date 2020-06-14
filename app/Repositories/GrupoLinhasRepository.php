<?php

namespace App\Repositories;

use App\Models\GrupoLinhas;
use InfyOm\Generator\Common\BaseRepository;

/**
 * Class GrupoLinhasRepository
 * @package App\Repositories
*/
class GrupoLinhasRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'name',
        'tipo',
        'tipodia'
    ];

    /**
     * @return string
     */
    public function model()
    {
        return GrupoLinhas::class;
    }
}
