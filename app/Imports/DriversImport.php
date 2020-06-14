<?php

namespace App\Imports;

use App\Models\Drivers;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithCustomCsvSettings;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class DriversImport implements ToModel, WithCustomCsvSettings, WithColumnFormatting, WithHeadingRow
{
    public function model(array $row)
    {
        $check_driver = Drivers::where('extended_id', $row['extended_id'])
                        ->first();
        $last_driver = Drivers::orderBy('id', 'DESC')->first();

        if ($check_driver) {
            $driver = Drivers::find($check_driver->id);
            $driver->name = $row[ 'name'];
            $driver->employee_number = $row['employee_number'];
            $driver->extended_id = $row['extended_id'];
            $driver->telefone = $row['telefone'];
            $driver->cpf = $row['cpf'];
            $driver->password = substr($row['cpf'], -4);
            $driver->save();

        } else {
            return new Drivers([
                'name' => $row['name'],
                'dealer_id' => 0,
                'group_id' => $last_driver->group_id,
                'subgroup_id' => $last_driver->subgroup_id,
                'site_id' => $last_driver->site_id,
                'employee_number' => $row['employee_number'],
                'extended_id' => $row['extended_id'],
                'telefone' => $row['telefone'],
                'cpf' => $row['cpf'],
                'password' => substr($row['cpf'], -4),
                'tipo' => 'motorista'
            ]);
        }
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

        ];
    }
}
