<?php

namespace App\DataTables;

use App\Models\Params;
use App\Models\Linhas;
use App\Models\Drivers;
use App\Models\DatatablesOrder;
use Yajra\DataTables\Services\DataTable;
use Yajra\DataTables\EloquentDataTable;

class LinhasDataTable extends DataTable
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

        return $dataTable->addColumn('action', 'linhas.datatables_actions');

    }

    /**
     * Get query source of dataTable.
     *
     * @param \App\Models\Post $model
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function query(Linhas $model)
    {
        return $model->newQuery();
    }

    /**
     * Optional method if you want to use html builder.
     *
     * @return \Yajra\DataTables\Html\Builder
     */
    public function html()
    {
        if (\Auth::user()->can('add_linhas')) {

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
                        ['extend' => 'create', 'className' => 'btn btn-default btn-sm no-corner', ],
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

        $datatables_order = DatatablesOrder::where('model', 'linhas')
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
                    'name' => 'codigo',
                    'data' => 'codigo',
                    'title' => 'Código',
                    'orderable' => true
                ],
                [
                    'name' => 'nome_fantasia',
                    'data' => 'nome_fantasia',
                    'title' => 'Nome Fantasia',
                    'orderable' => true
                ],
                [
                    'name' => 'nome',
                    'data' => 'nome',
                    'title' => 'Nome',
                    'orderable' => true
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
        return 'linhasdatatable_' . time();
    }
}