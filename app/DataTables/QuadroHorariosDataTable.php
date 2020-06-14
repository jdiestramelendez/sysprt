<?php

namespace App\DataTables;

use App\Models\QuadroHorarios;
use App\Models\DatatablesOrder;
use Yajra\DataTables\Services\DataTable;
use Yajra\DataTables\EloquentDataTable;

class QuadroHorariosDataTable extends DataTable
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

        return $dataTable->addColumn('action', 'quadro_horarios.datatables_actions');
    }

    /**
     * Get query source of dataTable.
     *
     * @param \App\Models\QuadroHorarios $model
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function query(QuadroHorarios $model)
    {
        return $model->newQuery()->select(
                    'quadro_horarios.id',
                    'quadro_horarios.linha',
                    'quadro_horarios.dia_tipo',
                    'quadro_horarios.planejamento',
                    'quadro_horarios.objetivo_do_quadro',
                    'quadro_horarios.frota',
                    'quadro_horarios.viagens',
                    'quadro_horarios.velocidade',
                    'quadro_horarios.codigo_de_garagem',
                    'quadro_horarios.sequencia_viagem',
                    'quadro_horarios.posicao',
                    'quadro_horarios.saida_da_garagem',
                    'quadro_horarios.chegada_no_terminal',
                    'quadro_horarios.ida_ou_volta',
                    'quadro_horarios.codigo_de_tp1',
                    'quadro_horarios.codigo_de_ts36');
    }

    /**
     * Optional method if you want to use html builder.
     *
     * @return \Yajra\DataTables\Html\Builder
     */
    public function html()
    {
        if (\Auth::user()->can('add_quadrohorarios')) {

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
        $datatables_order = DatatablesOrder::where('model', 'quadro_horarios')
            ->where('user', \Auth::user()->id)
            ->first();

        $array_datatables = [];

        if($datatables_order) {

            $names  = explode(',', $datatables_order->names);
            $titles = explode(',', $datatables_order->titles);
            $model  = $datatables_order->model;

            for ($i=0; $i  < count($names); $i++) {
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
                ],                   [
                    'name' => 'planejamento',
                    'data' => 'planejamento',
                    'title' => 'Planejamento',
                    'orderable' => true
                ],
                [
                    'name' => 'objetivo_do_quadro',
                    'data' => 'objetivo_do_quadro',
                    'title' => 'Objetivo',
                    'orderable' => true
                ],                
                [
                    'name' => 'frota',
                    'data' => 'frota',
                    'title' => 'Frota',
                    'orderable' => true
                ],
                [
                    'name' => 'viagens',
                    'data' => 'viagens',
                    'title' => 'Viagens',
                    'orderable' => true
                ],
                [
                    'name' => 'velocidade',
                    'data' => 'velocidade',
                    'title' => 'Velocidade',
                    'orderable' => true
                ],
                [
                    'name' => 'codigo_de_garagem',
                    'data' => 'codigo_de_garagem',
                    'title' => 'Código Garagem',
                    'orderable' => true
                ],
                [
                    'name' => 'sequencia_viagem',
                    'data' => 'sequencia_viagem',
                    'title' => 'Sequência Viagem',
                    'orderable' => true
                ],
                [
                    'name' => 'posicao',
                    'data' => 'posicao',
                    'title' => 'Posição',
                    'orderable' => true
                ],
                [
                    'name' => 'saida_da_garagem',
                    'data' => 'saida_da_garagem',
                    'title' => 'Saída Garagem',
                    'orderable' => true
                ],
                [
                    'name' => 'chegada_no_terminal',
                    'data' => 'chegada_no_terminal',
                    'title' => 'Chegada terminal',
                    'orderable' => true
                ],
                [
                    'name' => 'ida_ou_volta',
                    'data' => 'ida_ou_volta',
                    'title' => 'Ida / Volta',
                    'orderable' => true
                ],
                [
                    'name' => 'codigo_de_tp1',
                    'data' => 'codigo_de_tp1',
                    'title' => 'Código TP1',
                    'orderable' => true
                ],
                [
                    'name' => 'codigo_de_ts36',
                    'data' => 'codigo_de_ts36',
                    'title' => 'Codigo TS36',
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
        return 'quadro_horariosdatatable_' . time();
    }
}
