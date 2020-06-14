<?php

namespace App\Imports;

use App\Models\Escalas;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithCustomCsvSettings;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;

class EscalasImport implements ToModel, WithCustomCsvSettings, WithColumnFormatting
{
    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function model(array $row)
    {

      // $date_formated = \Carbon\Carbon::parse($row[0])->format('Y-m-d');

      return new Escalas([
          'data'                  => \Carbon\Carbon::createFromFormat('d/m/Y', $row[0]),
          'linha'                 => $row[1],
          'dia_tipo'              => $row[2],
          'planejamento'          => $row[3],
          'numero_de_equipes'     => $row[4],
          'carro'                 => $row[5],
          'motorista'             => $row[6],
          'cobrador'              => $row[7]
      ]);
    }

    public function getCsvSettings() : array
    {
        return [
            'input_encoding' => 'ISO-8859-1'
        ];
    }

    public function columnFormats(): array
    {
        return [
            // 'A' => NumberFormat::FORMAT_DATE_DDMMYYYY,
        ];
    }
}
