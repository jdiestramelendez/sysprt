<?php

namespace App\Exports;

use App\Models\UnitInformation;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class UnitsExport implements FromCollection, WithHeadings, ShouldAutoSize
{
    public function collection()
    {
        return UnitInformation::all();
    }

    public function headings(): array
    {
        return [
            'UnitID',
            'SerialNumber'
        ];
    }
}