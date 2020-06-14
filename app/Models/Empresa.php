<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Empresa extends Model
{
    public $table = 'Empresa';

    public $dateFormat = 'd-m-Y H:i:s';

    public $connection = 'conSistema';

    public $fillable =
    [
        'id',
        'nome',
        'banco',
        'totalveiculosativos',
        'datacadastro',
    ];
}
