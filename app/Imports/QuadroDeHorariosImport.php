<?php

namespace App\Imports;

use App\Models\QuadroDeHorarios;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithCustomCsvSettings;

class QuadroDeHorariosImport implements ToModel, WithCustomCsvSettings
{
    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function model(array $row)
    {
        return new QuadroDeHorarios([
            'linha'                 => $row[0],
            'dia_tipo'              => $row[1],
            'planejamento'          => $row[3],
            'objetivo_do_quadro'    => $row[4],
            'frota'                 => $row[5],
            'viagens'               => $row[6],
            'velocidade'            => $row[7],
            'codigo_de_garagem'     => $row[8],
            'sequencia_viagem'      => $row[9],
            'posicao'               => $row[10],
            'saida_da_garagem'      => $row[11],
            'chegada_no_terminal'   => $row[12],
            'ida_ou_volta'          => $row[15],
            'codigo_de_tp1'         => $row[17],
            'codigo_de_ts36'        => $row[18]
        ]);
    }

    public function getCsvSettings() : array
    {
        return [
            'input_encoding' => 'ISO-8859-1'
        ];
    }
}
