<?php

namespace App\Exports;

use App\Models\Drivers;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;


class DriversExport implements FromCollection, WithHeadings, ShouldAutoSize
{
    public function collection()
    {
        return Drivers::select('name','employee_number','extended_id','telefone','cpf')->get();
    }

    public function headings(): array
    {
        return [
            'name',
            'employee_number',
            'extended_id',
            'telefone',
            'cpf'
    ];
    }
}