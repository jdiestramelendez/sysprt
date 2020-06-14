<?php

namespace App\Exports;

use App\Models\ReportDetailsEvents;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\Exportable;

class ReportDetailsEventExport implements FromQuery
{
  use Exportable;

    public function __construct(array $params)
    {
      $this->ids = $params[0];
      $this->periodo = $params[1];
    }

    public function query()
    {
      return ReportDetailsEvents::query();

      return ReportDetailsEvents::query()
        ->whereIn("motoristaid", $this->ids)
        ->where("datainicio", ">=", \Carbon\Carbon::parse(explode(',', $this->periodo)[0].' '.'00:00:00')->format('Y-m-d H:i:s'))
        ->where("datafinal", "<=", \Carbon\Carbon::parse(explode(',', $this->periodo)[1].' '.'23:59:00')->format('Y-m-d H:i:s'))
        ->noLock();
    }

    // public function headings(): array
    // {
    //     return [
    //         'serial_unit',
    //         'id_unit',
    //         'description',
    //         'registration_number',
    //         'model',
    //         'year',
    //         'chassi',
    //         'consumo',
    //         'capacidade',
    //         'passageiros',
    //         'notes',
    //         'fuel',
    // ];
    // }
}