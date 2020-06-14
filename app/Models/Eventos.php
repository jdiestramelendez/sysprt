<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Eventos extends Model
{
    public $table = 'eventos';

    public $dateFormat = 'Y-m-d H:i:s.u';

    public $connection = 'conEmpresa';

    public $fillable =
    [
        'id',
        'nome',
        'valor',
        'idinicio',
        'idfim',
        'ativo'
    ];
}
