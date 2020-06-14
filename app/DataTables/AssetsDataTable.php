<?php

namespace App\DataTables;

use App\Models\Params;
use App\Models\Assets;
use App\Models\Drivers;
use App\Models\DatatablesOrder;
use Yajra\DataTables\Services\DataTable;
use Yajra\DataTables\EloquentDataTable;

class AssetsDataTable extends DataTable
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

        $params = Params::join('fuso_list', 'fuso_list.id', '=', 'params.fuso')->where('user_id', \Auth::user()->id)->first();

        return $dataTable->addColumn('action', 'assets.datatables_actions')
            ->editColumn('UltimaPosicao', function ($id_unit) use ($params){
                if ($id_unit->UltimaPosicao != NULL) {
                    return \Carbon\Carbon::parse($id_unit->UltimaPosicao)->timezone($params->carbon_fuso_name)->format('d/m/Y H:i');
                } else {
                    return "";
                }
            })
            ->editColumn('InicioUltimaViagem', function ($id_unit) use ($params){
                if ($id_unit->InicioUltimaViagem != NULL) {
                    return \Carbon\Carbon::parse($id_unit->InicioUltimaViagem)->timezone($params->carbon_fuso_name)->format('d/m/Y H:i');
                } else {
                    return "";
                }
            })
            ->editColumn('state', function ($id_unit) use ($params){
                switch ($id_unit->state) {
                    case 'vermelho':                
                    return  '<i class="fas fa-circle " title="Ultima Posição e Viagem à mais de 3 dias" style="color: red;"></i>';
                    break;
                    case 'amarelo':                      
                    return '<i class="fas fa-circle text-warning" title="Ultima Posição em até 3 dias e Ultima Viagem à mais de 3 dias" style="color: yellow;"></i>';
                    break;
                    case 'verde':                       
                    return  '<i class="fas fa-circle text-success" title="Ultima Posição e Viagem em até 3 dias" style="color: green;"></i>';
                    break;
                } 
            })
            ->editColumn('FimUltimaViagem', function ($id_unit) use ($params){
                if ($id_unit->FimUltimaViagem != NULL) {
                    return \Carbon\Carbon::parse($id_unit->FimUltimaViagem)->timezone($params->carbon_fuso_name)->format('d/m/Y H:i');
                } else {
                    return "";
                }
            })
            ->editColumn('id_unit_timeline', function ($id_unit){
                $date = date("Y-m-d");
                $dropID = "<a title='Ver na timeline de eventos' href='/timeline_eventos?unitid=" . $id_unit->id_unit . "&date=" . $date ."&time=00:00' class='bubbleButton'><i class='icon fal fa-sliders-h'></i><span></span></a>";
                return  $dropID;
            })
            ->editColumn('id_unit_detalhes', function ($id_unit){
                $date = date("Y-m-d");
                $dropDetails = "<a title='Ver no detalhes da viagem' href='/detalhes_viagem?unitid=" . $id_unit->id_unit . "&start=" . $date ."&end=" . $date ."' class='bubbleButton'><i class='icon fal fa-map-signs'></i><span></span></a>";
                return  $dropDetails;
            })->rawColumns(['state','id_unit_timeline','id_unit_detalhes','action']);

    }

    /**
     * Get query source of dataTable.
     *
     * @param \App\Models\Post $model
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function query(Assets $model)
    {

        return $model->newQuery()
            ->leftJoin('sub_groups', 'assets.subgroup_id', 'sub_groups.id')
            ->leftJoin('sites', 'assets.site_id', 'sites.id')
            ->leftJoin('LastTrips', 'LastTrips.assetid', 'assets.id')
            ->leftJoin('LastPositions', 'LastPositions.assetid', 'assets.id')
            ->leftJoin('drivers as dp', 'dp.extended_id', 'LastPositions.driverid')
            ->leftJoin('drivers as dt', 'dt.extended_id', 'lastTrips.driverid')
            ->select(
                'sub_groups.name as nome_subgrupo',
                'sites.name as nome_site',
                'assets.id',
                'assets.serial_unit',
                'assets.id_unit',
                'assets.description',
                'assets.registration_number',
                'assets.device',
                'assets.status',
                'LastTrips.TripStart as InicioUltimaViagem',
                'LastTrips.TripEnd as FimUltimaViagem',
                'LastPositions.Timestamp as UltimaPosicao',
                'LastPositions.Odometer as Odometro',
                \DB::raw('case when (LastTrips.EndOdometer - LastTrips.StartOdometer) < 0 then 0 else (LastTrips.EndOdometer - LastTrips.StartOdometer)  end  / 1000.0 as KmUltimaViagem'),
                \DB::raw('IIF(dp.name is null, cast(LastPositions.driverid as varchar), dp.name) as UltimoMotoristaPosition'),
                \DB::raw('IIF(dt.name is null, cast(LastPositions.driverid as varchar), dt.name) as UltimoMotoristaViagem'),
                \DB::raw(
                    "case when(datediff(hour,LastPositions.Timestamp,getdate()) >= 72) then 
                    'vermelho' 
                    else    case when (datediff(hour,LastTrips.TripEnd,getdate()) >= 72) then 'amarelo' 
                    else 'verde' end end as state") 
            )
            ->where('sites.deleted_at', NULL)
            ->where('sub_groups.deleted_at', NULL)
            ->where('assets.deleted_at', NULL)
            ->where('dp.deleted_at', NULL) ;
            // return $model->newQuery()
            //     ->with(['subgroup', 'site', 'lastposition', 'lasttrip'])
            //     ->select('assets.*');
    }

    /**
     * Optional method if you want to use html builder.
     *
     * @return \Yajra\DataTables\Html\Builder
     */
    public function html()
    {
        if (\Auth::user()->can('add_assets')) {

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
                        ['extend' => 'reload', 'className' => 'btn btn-default btn-sm no-corner', ],
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
        $datatables_order = DatatablesOrder::where('model', 'assets')
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
                    'name' => 'state',
                    'data' => 'state',
                    'title' => 'Info',
                    'orderable' => false,
                    'searchable' => false
                ],
                [
                    'name' => 'sub_groups.name',
                    'data' => 'nome_subgrupo',
                    'title' => 'Subgrupo',
                    'orderable' => true,
                    'searchable' => false
                ],
                [
                    'name' => 'sites.name',
                    'data' => 'nome_site',
                    'title' => 'Site',
                    'orderable' => true,
                    'searchable' => false
                ],
                [
                    'name' => 'serial_unit',
                    'data' => 'serial_unit',
                    'title' => 'Serial',
                    'orderable' => true
                ],
                [
                    'name' => 'id_unit',
                    'data' => 'id_unit',
                    'title' => 'Unit Id',
                    'orderable' => true
                ],
                [
                    'name' => 'description',
                    'data' => 'description',
                    'title' => 'Descrição',
                    'orderable' => true
                ],
                [
                    'name' => 'registration_number',
                    'data' => 'registration_number',
                    'title' => 'Registro',
                    'orderable' => true
                ],
                [
                    'name' => 'device',
                    'data' => 'device',
                    'title' => 'Dispositivo',
                    'orderable' => true
                ],
                [
                    'name' => 'status',
                    'data' => 'status',
                    'title' => 'Status',
                    'orderable' => true
                ],
                [
                    'name' => 'UltimaPosicao',
                    'data' => 'UltimaPosicao',
                    'title' => 'Última posição',
                    'orderable' => true,
                    'searchable' => false,
                    'render' => 'sortByDate(data, type, full, meta);'
                ],
                [
                    'name' => 'UltimoMotoristaPosition',
                    'data' => 'UltimoMotoristaPosition',
                    'title' => 'Motorista Atual',
                    'orderable' => false,
                    'searchable' => false
                ],
                [
                    'name' => 'InicioUltimaViagem',
                    'data' => 'InicioUltimaViagem',
                    'title' => 'Início última viagem',
                    'orderable' => true,
                    'searchable' => false,
                    'render' => 'sortByDate(data, type, full, meta);'
                ],
                [
                    'name' => 'FimUltimaViagem',
                    'data' => 'FimUltimaViagem',
                    'title' => 'Fim última viagem',
                    'orderable' => true,
                    'searchable' => false,
                    'render' => 'sortByDate(data, type, full, meta);'
                ],
                [
                    'name' => 'KmUltimaViagem',
                    'data' => 'KmUltimaViagem',
                    'title' => 'Km última viagem',
                    'orderable' => false,
                    'searchable' => false
                ],
                [
                    'name' => 'Odometro',
                    'data' => 'Odometro',
                    'title' => 'Odômetro',
                    'orderable' => false,
                    'searchable' => false
                ],
                [
                    'name' => 'UltimoMotoristaViagem',
                    'data' => 'UltimoMotoristaViagem',
                    'title' => 'Último Motorista',
                    'orderable' => false,
                    'searchable' => false
                ],
                [
                    'name' => 'id_unit_timeline',
                    'data' => 'id_unit_timeline',
                    'title' => '',
                    'searchable' => false,
                    'orderable' => false
                ],
                [
                    'name' => 'id_unit_detalhes',
                    'data' => 'id_unit_detalhes',
                    'title' => '',
                    'searchable' => false,
                    'orderable' => false
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
        return 'assetsdatatable_' . time();
    }
}
