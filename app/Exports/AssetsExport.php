<?php

namespace App\Exports;

use App\Models\Assets;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;


class AssetsExport implements FromCollection, WithHeadings, ShouldAutoSize
{
    public function collection()
    {
        return Assets::select('serial_unit','id_unit','description','registration_number','model','year','chassi','consumo','capacidade','passageiros','notes', 'fuel')->get();;
    }

    public function headings(): array
    {
        return [
            'serial_unit',
            'id_unit',
            'description',
            'registration_number',
            'model',
            'year',
            'chassi',
            'consumo',
            'capacidade',
            'passageiros',
            'notes',
            'fuel',
    ];
    }
}