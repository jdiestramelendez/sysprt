<?php

namespace App\DataTables;

use App\Models\UnitInformation;
use App\Models\DatatablesOrder;
use Yajra\DataTables\Services\DataTable;
use Yajra\DataTables\EloquentDataTable;

class UnitInformationDataTable extends DataTable
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

    return $dataTable->addColumn('action', 'unit_information.datatables_actions');
  }

  /**
   * Get query source of dataTable.
   *
   * @param \App\Models\Post $model
   * @return \Illuminate\Database\Eloquent\Builder
   */
  public function query(UnitInformation $model)
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
        if (\Auth::user()->can('add_unit_informations')) {

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

  /**
   * Get columns.
   *
   * @return array
   */
  protected function getColumns()
  {
    $datatables_order = DatatablesOrder::where('model', 'unit_informations')
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
          'name' => 'UnitID',
          'data' => 'UnitID',
          'title' => 'ID da Unidade',
          'orderable' => true
        ],
        [
          'name' => 'SerialNumber',
          'data' => 'SerialNumber',
          'title' => 'Serial',
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
    return 'unit_information_datatable_' . time();
  }
}
