<?php

namespace App\Imports;

use App\Models\PontosParada;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithCustomCsvSettings;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;

class PontosImport implements ToModel, WithCustomCsvSettings, WithColumnFormatting
{
    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function model(array $row)
    {

      // $date_formated = \Carbon\Carbon::parse($row[0])->format('Y-m-d');

      return new PontosParada([
          'endereco'            => $row[0],
          'tipo'                => $row[1],
          'codigo_referencia'   => $row[2],
          'cerca'               => $row[3],
          'lat'                 => $row[4],
          'lng'                 => $row[5],
          'nome'                => $row[6]
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
