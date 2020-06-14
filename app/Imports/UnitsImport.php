<?php

namespace App\Imports;

use App\Models\UnitInformation;
use App\Models\OrganizationUnits;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithCustomCsvSettings;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class UnitsImport implements ToModel, WithCustomCsvSettings, WithColumnFormatting, WithHeadingRow
{
    public function model(array $row)
    {
       $check_unit = UnitInformation::where('UnitID', (int)$row['unitid'])->first();

       $check_exist = OrganizationUnits::where('UnitId', (int)$row['unitid'])->first();

        if($check_exist) {

        } else {
            $to_save                = new OrganizationUnits;
            $to_save->OrgId         = session()->get('selected_group_id');
            $to_save->UnitId        = (int)$row['unitid'];
            $to_save->SerialNumber  = $row['serialnumber'];
            $to_save->save();
        }

        if ($check_unit) {
            $check_unit->UnitID = (int)$row['unitid'];
            $check_unit->SerialNumber = $row['serialnumber'];
            $check_unit->save();

        } else {
            return new UnitInformation([
                'UnitID' => (int)$row['unitid'],
                'SerialNumber' => $row['serialnumber']
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
