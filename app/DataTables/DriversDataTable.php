<?php

namespace App\DataTables;

use App\Models\Drivers;
use App\Models\DatatablesOrder;
use App\Models\LastDriverPosition;
use App\Models\LastTrips;
use Yajra\DataTables\Services\DataTable;
use Yajra\DataTables\EloquentDataTable;
use App\Models\Params;

class DriversDataTable extends DataTable
{
    /**
     * Build DataTable class.
     *
     * @param mixed $query Results from query() method.
     * @return \Yajra\DataTables\DataTableAbstract
     */
    public function dataTable($query)
    {
        $dataTable = new EloquentDataTable($query);

        return $dataTable->addColumn('action', 'drivers.datatables_actions')
            ->editColumn('lastpositionasset.UnitId', function ($UnitId) {
                if (isset($UnitId->lastposition->UnitId)) {

                    $asset = LastDriverPosition::join('assets', 'assets.id_unit', '=', 'LastDriverPosition.UnitId')
                    ->where('assets.id_unit', $UnitId->lastposition->UnitId)->first();

                    if (isset($asset->description)) {
                        return $asset->description;

                    } else {
                        return '';
                    }

                } else {
                    return '';
                }
            })
            ->editColumn('lasttrip.TripStart', function ($UnitId) {
                $params = Params::join('fuso_list', 'fuso_list.id', '=', 'params.fuso')->where('user_id', \Auth::user()->id)->first();

                if (isset($UnitId->lastposition->Timestamp)) {
                    return \Carbon\Carbon::parse($UnitId->lastposition->Timestamp)
                                            ->timezone($params->carbon_fuso_name)
                                            ->format('d/m/Y H:i');
                } else {
                    return '';
                }
            })
            ->editColumn('lastposition.Latitude', function ($UnitId) {

                if (isset($UnitId->lastposition->Latitude) && isset($UnitId->lastposition->Longitude)) {
                    $latitude   = $UnitId->lastposition->Latitude;
                    $longitude  = $UnitId->lastposition->Longitude;

                    $getAddressParams = '?' . 'prox=' . $latitude . ',' . $longitude . '&mode=retrieveAddress' . '&maxResults=1' . '&app_id=' . 'Cl3yhvKlZBXDoNm7CYQI' . '&app_code=' . 'EoSlW-Ic_doByCeaMqN4Bg';

                    $cURL = curl_init();
                    $setopt_array = array(CURLOPT_URL => "https://reverse.geocoder.api.here.com/6.2/reversegeocode.json".$getAddressParams,
                        CURLOPT_RETURNTRANSFER => true,
                        CURLOPT_HTTPHEADER => array());
                    curl_setopt_array($cURL, $setopt_array);
                    $json_response_data = curl_exec($cURL);
                    if(isset(["Response"]["View"][0]["Result"][0]["Location"]["Address"]["Label"])) {
                        return json_decode($json_response_data, true)["Response"]["View"][0]["Result"][0]["Location"]["Address"]["Label"];
                    } else {
                        return '';
                    }
                    curl_close($cURL);

                } else {
                    return '';
                }

            });
        
    }

    /**
     * Get query source of dataTable.
     *
     * @param \App\Models\Post $model
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function query(Drivers $model)
    {
        return $model->newQuery()->select('drivers.*')->with(['subgroup', 'site', 'lastposition']);

    }

    /**
     * Optional method if you want to use html builder.
     *
     * @return \Yajra\DataTables\Html\Builder
     */
    public function html()
    {
        if (\Auth::user()->can('add_drivers')) {

            return $this->builder()
                ->columns($this->getColumns())
                ->minifiedAjax()
                ->addAction(['title' => ''])
                ->parameters([
                    'colReorder' => true,
                    'dom'     => 'Bfrtip',
                    'order'   => [[0, 'desc']],
                    'buttons' => [
                        ['extend' => 'colvis', 'columns' => ':not(.noVis)', 'text' => 'Colunas', 'className' => 'btn btn-default btn-sm no-corner', ],
                        ['extend' => 'create', 'className' => 'btn btn-default btn-sm no-corner',],
                        ['extend' => 'reload', 'className' => 'btn btn-default btn-sm no-corner',],
                        ['extend' => 'excel', 'className' => 'btn btn-default btn-sm no-corner', ],
                        ['extend' => 'reset', 'className' => 'btn btn-default btn-sm no-corner', ],
                    ],
                    'language' => ['url' => 'lang/language.ptbr.json']
                ]);
        } else {
            return $this->builder()
                ->columns($this->getColumns())
                ->minifiedAjax()
                ->addAction(['title' => ''])
                ->parameters([
                    'colReorder' => true,
                    'dom' => 'Bfrtip',
                    'order' => [[0, 'desc']],
                    'buttons' => [
                        ['extend' => 'colvis', 'columns' => ':not(.noVis)', 'text' => 'Colunas', 'className' => 'btn btn-default btn-sm no-corner', ],
                        ['extend' => 'reload', 'className' => 'btn btn-default btn-sm no-corner', ],
                        ['extend' => 'excel', 'className' => 'btn btn-default btn-sm no-corner', ],
                        ['extend' => 'reset', 'className' => 'btn btn-default btn-sm no-corner', ],
                    ],
                    'language' => ['url' => 'lang/language.ptbr.json']
                ]);
        }
    }

    /**
     * Get columns.
     *
     * @return array
     */
    protected function getColumns()
    {

        $datatables_order = DatatablesOrder::where('model', 'drivers')
            ->where('user', \Auth::user()->id)
            ->first();

        $array_datatables = [];

        if($datatables_order) {

            $names  = explode(',', $datatables_order->names);
            $titles = explode(',', $datatables_order->titles);
            $model  = $datatables_order->model;

            for ($i=0; $i < count($names); $i++) {
                array_push(
                    $array_datatables,
                    [
                        'name' => $names[$i],
                        'data' => $names[$i],
                        'title' => $titles[$i],
                        'orderable' => true
                    ]
                );
            }

            return $array_datatables;

        } else {

            return [
                [
                    'data' => 'name',
                    'title' => 'Nome',
                    'orderable' => true
                ],
                [
                    'data' => 'employee_number',
                    'title' => 'Registro Empregado',
                    'orderable' => true
                ],
                [
                    'data' => 'extended_id',
                    'title' => 'Extended ID',
                    'orderable' => true
                ],
                [
                    'data' => 'subgroup.name',
                    'title' => 'Subgrupo',
                    'orderable' => true
                ],
                [
                    'data' => 'site.name',
                    'title' => 'Site',
                    'orderable' => true
                ],
                [
                    'name' => 'lastpositionasset.UnitId',
                    'data' => 'lastpositionasset.UnitId',
                    'title' => 'Último Veículo',
                    'orderable' => false,
                    'searchable' => false
                ],
                [
                    'name' => 'lastposition.Latitude',
                    'data' => 'lastposition.Latitude',
                    'title' => 'Última Posição',
                    'orderable' => false,
                    'searchable' => false
                ],
                [
                    'name' => 'lasttrip.TripStart',
                    'data' => 'lasttrip.TripStart',
                    'title' => 'Última Viagem',
                    'orderable' => false,
                    'searchable' => false
                ]
            ];
        }
    }

    /**
     * Get filename for export.
     *
     * @return string
     */
    protected function filename()
    {
        return 'driversdatatable_' . time();
    }
}
