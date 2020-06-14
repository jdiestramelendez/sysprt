<?php

namespace App\DataTables;

use App\Models\Escalas;
use App\Models\DatatablesOrder;
use Yajra\DataTables\Services\DataTable;
use Yajra\DataTables\EloquentDataTable;

class EscalasDataTable extends DataTable
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

        return $dataTable->addColumn('action', 'escalas.datatables_actions');
    }

    /**
     * Get query source of dataTable.
     *
     * @param \App\Models\Escalas $model
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function query(Escalas $model)
    {
        // var_dump($model->newQuery());
        return $model->newQuery()->select('escalas.id', 'escalas.data','escalas.linha', 'escalas.dia_tipo', 'escalas.planejamento', 'escalas.numero_de_equipes','escalas.carro', 'escalas.motorista', 'escalas.cobrador');

        return $model->newQuery();
    }

    public function html()
    {
        if (\Auth::user()->can('add_escalas')) {

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
                        ['extend' => 'reset', 'className' => 'btn btn-default btn-sm no-corner', ]
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
                        ['extend' => 'reset', 'className' => 'btn btn-default btn-sm no-corner', ]
                    ],
                    'language' => ['url' => 'lang/language.ptbr.json']
                ]);
        }
    }

    protected function getColumns()
    {
        $datatables_order = DatatablesOrder::where('model', 'escalas')
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
                    'name' => 'data',
                    'data' => 'data',
                    'title' => 'Data',
                    'orderable' => true
                ],
                [
                    'name' => 'linha',
                    'data' => 'linha',
                    'title' => 'Linha',
                    'orderable' => true
                ],
                [
                    'name' => 'dia_tipo',
                    'data' => 'dia_tipo',
                    'title' => 'Tipo de Dia',
                    'orderable' => true
                ],
                [
                    'name' => 'planejamento',
                    'data' => 'planejamento',
                    'title' => 'Planejamento',
                    'orderable' => true
                ],
                [
                    'name' => 'numero_de_equipes',
                    'data' => 'numero_de_equipes',
                    'title' => 'Número de Equipes',
                    'orderable' => true
                ],
                [
                    'name' => 'carro',
                    'data' => 'carro',
                    'title' => 'Carro',
                    'orderable' => true
                ],     
                [
                    'name' => 'motorista',
                    'data' => 'motorista',
                    'title' => 'Motorista',
                    'orderable' => true
                ],
                [
                    'name' => 'cobrador',
                    'data' => 'cobrador',
                    'title' => 'Cobrador',
                    'orderable' => true
                ],
                
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
        return 'escalasdatatable_' . time();
    }
}
