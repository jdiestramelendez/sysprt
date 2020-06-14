<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EventosParametros extends Model
{
    public $dateFormat = 'd-m-Y H:i:s';

    public $connection = 'conEmpresa';

    public $table = 'parametros';

    public $fillable =
    [
        'idEvento',
        'idParametro',
        'valor',
        'descricaoParametro'
    ];

}
