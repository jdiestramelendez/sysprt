<?php

namespace App\DataTables;

use App\User;
use App\Models\Group;
use Yajra\DataTables\Services\DataTable;
use Yajra\DataTables\EloquentDataTable;

class UsersDataTable extends DataTable
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

        return $dataTable->addColumn('action', 'user.datatables_actions')
          ->editColumn('group_id', function ($group) {
            if (isset($group->group_id)) {
              $res = [];

              $group_names = Group::whereIn('id', explode(",", $group->group_id))->select('name')->get();

              for ($i=0; $i < count($group_names); $i++) {
                array_push($res, $group_names[$i]->name);
              }

              return implode(',', $res);

            }
          });
    }

    /**
     * Get query source of dataTable.
     *
     * @param \App\Models\Post $model
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function query(User $model)
    {

      if (\Auth::user()->hasRole('Admin')) {

        $result = $model->newQuery()->select('users.name as name', 'users.id', 'users.email', 'users.group_id');

      } elseif (\Auth::user()->hasRole('Dealer')) {

        $groups_array = [];

        $groups = User::join('groups', 'users.id', '=', 'groups.dealer_id')
          ->select('groups.id')
          ->where('groups.dealer_id', \Auth::user()->id)->get();

          if(count($groups) > 0) {
            foreach ($groups as $value) {
              array_push($groups_array, $value->id);
            }

            $result = $model->newQuery()->where(function ($q) use ($groups_array) {
              foreach ($groups_array as $device) {
                $q->orWhere("users.group_id", "like", "%" . $device . "%")->role(['Group', 'Subgroup']);
              }
            });

          } else {

            $result = $model->newQuery()->where('id', '');

          }
        
      

      }

      return $result;

    }

    /**
     * Optional method if you want to use html builder.
     *
     * @return \Yajra\DataTables\Html\Builder
     */
    public function html()
    {
        if (\Auth::user()->can('add_users')) {

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
      if (\Auth::user()->hasRole('Admin') || \Auth::user()->hasRole('Dealer')) {

        return [
          [
            'name' => 'users.name',
            'data' => 'name',
            'title' => 'Nome',
            'orderable' => true
          ],
          [
            'data' => 'email',
            'title' => 'Email',
            'orderable' => true
          ],
          [
            'name' => 'group_id',
            'data' => 'group_id',
            'title' => 'Grupos',
            'orderable' => true,
            'searchable' => false
          ]
        ];

      } else {
          return [
          [
            'data' => 'name',
            'title' => 'Nome',
            'orderable' => true
          ],
          [
            'data' => 'email',
            'title' => 'Email',
            'orderable' => true
          ]
        ];
      }

      // if (\Auth::user()->hasRole('Dealer')) {
      //   return [
      //     [
      //       'data' => 'name',
      //       'title' => 'Nome',
      //       'orderable' => true
      //     ],
      //     [
      //       'data' => 'email',
      //       'title' => 'Email',
      //       'orderable' => true
      //     ]
      //   ];
      // }
        
    }

    /**
     * Get filename for export.
     *
     * @return string
     */
    protected function filename()
    {
        return 'usersdatatable_' . time();
    }
}