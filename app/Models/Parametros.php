<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Parametros extends Model
{
    public $dateFormat = 'd-m-Y H:i:s';

    public $connection = 'conEmpresa';

    public $table = 'parametros';

    public $fillable =
    [
        'id',
        'descricao',
        'texto'
    ];
}
