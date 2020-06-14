<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Motoristas extends Model
{
    public $dateFormat = 'd-m-Y H:i:s';

    public $connection = 'conEmpresa';

    public $table = 'motoristas';

    public $fillable =
    [
        'id',
        'matricula',
        'nomecompleto',
        'nomedeescala',
        'cpf',
        'datanascimento',
        'idcracha',
    ];
}
