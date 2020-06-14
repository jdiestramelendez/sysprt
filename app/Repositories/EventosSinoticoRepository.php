<?php

namespace App\Repositories;

use App\Models\EventosSinotico;
use InfyOm\Generator\Common\BaseRepository;

/**
 * Class GrupoLinhasRepository
 * @package App\Repositories
*/
class EventosSinoticoRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
            'tipo'
           ,'startdatetime'
           ,'enddatetime'
           ,'startpositionid'
           ,'endpositionid'
           ,'horatratativa'
           ,'descricaotratativa'
           ,'usuariotratativa'
           ,'status'
           ,'assetId'
           ,'driverid'
           ,'lat'
           ,'lng'
           ,'recordedeventid'
           ,'linhaId'
           ,'iditinerario'
           ,'quadroshorariositensid'
           ,'quadrohorarioid'
           ,'descricaoevento'
           ,'tempoentreveiculos'
           ,'frontassetid'
    ];

    /**
     * @return string
     */
    public function model()
    {
        return EventosSinotico::class;
    }
}
