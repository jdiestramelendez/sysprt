<?php

namespace App\Imports;

use App\Models\Assets;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithCustomCsvSettings;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class AssetsImport implements ToModel, WithCustomCsvSettings, WithColumnFormatting, WithHeadingRow
{
    public function model(array $row)
    {
        $check_asset = Assets::where('registration_number', $row['registration_number'])->first();
        $last_asset = Assets::orderBy('id', 'DESC')->first();

        if ($check_asset) {
            $veiculo = Assets::find($check_asset->id);
            $veiculo->serial_unit = $row['serial_unit'];
            $veiculo->id_unit = $row['id_unit'];
            $veiculo->description = $row['description' ];
            $veiculo->registration_number = $row['registration_number'];
            $veiculo->model = $row['model'];
            $veiculo->year = $row['year'];
            $veiculo->chassi = $row['chassi'];
            $veiculo->consumo = $row['consumo'];
            $veiculo->capacidade = $row['capacidade'];
            $veiculo->passageiros = $row['passageiros'];
            $veiculo->notes = $row['notes'];
            $veiculo->fuel = $row['fuel'];
            $veiculo->save();

        } else {
            return new Assets([
                'id_unit' => $row['id_unit'],
                'description' => $row['description'],
                'registration_number' => $row['registration_number'],
                'model' => $row['model'],
                'dealer_id' => 0,
                'group_id' => $last_asset->group_id,
                'subgroup_id' => $last_asset->subgroup_id,
                'site_id' => $last_asset->site_id,
                'serial_unit' => 0,
                'device' => $last_asset->device,
                'status' => 'Disponível',
                'year' => $row['year'],
                'chassi' => $row['chassi'],
                'consumo' => $row['consumo'],
                'capacidade' => $row['capacidade'],
                'passageiros' => $row['passageiros'],
                'notes' => $row['notes'],
                'fuel' => $row['fuel']
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
