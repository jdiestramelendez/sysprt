<?php

namespace App\Http\Controllers\API;

use App\Authorizable;
use Illuminate\Http\Request;
use JWTAuth;
use JWTAuthException;
use App\Models\Group;
use App\Models\Events;
use App\Models\RifavSummary;
use App\Models\RifavSummaryAsset;
use App\Models\Drivers;
use App\Models\EventsRifav;
use App\Models\Assets;
use App\Models\Params;
use App\Models\RifavoParams;

class UserController extends \App\Http\Controllers\Controller
{
  use Authorizable;

  private function getToken($user)
  {
      $token = null;
      try {
        \Config::set('auth.defaults.guard', 'api' );

          if (!$token = JWTAuth::fromUser($user)) {
              return response()->json([
                  'response' => 'error',
                  'message' => 'Dados inválidos',
                  'token'=>$token
              ]);
          }
      } catch (JWTAuthException $e) {
          return response()->json([
              'response' => 'error',
              'message' => 'Erro ao tentar criar o token',
          ]);
      }
      return $token;
  }

  public function loginSystem(Request $request)
  {

    $user = \App\User::where('email', $request->email)->first();

    if ($user && \Hash::check($request->password, $user->password)) {

      $token = self::getToken($user);
      $user->auth_token = $token;
      $user->save();

      $response = ['success'=>true, 'user'=>$user, 'auth_token'=>$user->auth_token];
    } else {
      $response = ['success'=>false, 'data'=>'Usuário não existe'];

    }
    return response()->json($response, 201);

  }

  public function loginApp(Request $request)
  {

    $group_id = $request->matricula;

    $group = Group::find($group_id);

    if(!$group) {

      $response = ['success'=>false, 'data'=>'Empresa não encontrada.'];

      return response()->json($response, 201);

    } else {

      $configs      = \DB::table('configs')->first();
      $app_version = $configs->app_version;

      if(isset($request->app_version) &&  $request->app_version < $app_version) {
        $response = ['success'=>false, 'data'=>'Aplicativo desatualizado, visite a loja para atualizar. Obrigado.'];
        return response()->json($response, 201);
      }

      \Config::set('database.connections.sqlsrv_group.host', 'blustock.database.windows.net');
      \Config::set('database.connections.sqlsrv_group.username', 'AdminBluStock');
      \Config::set('database.connections.sqlsrv_group.password', 'Nz6cU4kAHtnCAFVq');
      \Config::set('database.connections.sqlsrv_group.database', $group->database_name);

      $driver = \App\Models\Drivers::select('*')->where('cpf', $request->cpf)->get()->first();
      if ($driver && $request->password === $driver->password) // The passwords match...
      {
          $token = self::getToken($driver);
          $driver->auth_token = $token;
          $driver->save();

          $response = ['success'=>true, 'driver'=>$driver, 'group'=>$group,'auth_token'=>$driver->auth_token];
      }
      else
        $response = ['success'=>false, 'data'=>'Usuário não existe'];

      return response()->json($response, 201);

    }

  }

  public function show()
  {
    return Auth::user();
  }

  public function getRifavo(Request $request)
  {
    $group = Group::where('id', $request->matricula)->first();
    // $params = Params::join('fuso_list', 'fuso_list.id', '=', 'params.fuso')->where('user_id', \Auth::user()->id)->first();
    $is = [1,2,3,4,6,7,8,9,10,11,14];

    \Config::set('database.connections.sqlsrv_group.host', 'blustock.database.windows.net');
    \Config::set('database.connections.sqlsrv_group.username', 'AdminBluStock');
    \Config::set('database.connections.sqlsrv_group.password', 'Nz6cU4kAHtnCAFVq');
    \Config::set('database.connections.sqlsrv_group.database', $group->database_name);

    $user_events = Events::join('LibraryEvents', 'LibraryEvents.EventId', '=', 'Events.EventId')
      ->whereIn('Events.EventId', $is)
      ->where('Events.DriverId', $request->user_id)
      ->where("Events.StartDateTime", ">=", \Carbon\Carbon::parse($request->data_inicio.' '.'00:00:00')->format('Y-m-d H:i:s'))
      ->where("Events.EndDateTime", "<=", \Carbon\Carbon::parse($request->data_termino.' '.'23:59:00')->format('Y-m-d H:i:s'))
      ->get();

      return $user_events;
  }

  public function getTotalRifavByDriverAndRangeDate(Request $request)
  {
    $group = Group::where('id', $request->matricula)->first();

    \Config::set('database.connections.sqlsrv_group.host', 'blustock.database.windows.net');
    \Config::set('database.connections.sqlsrv_group.username', 'AdminBluStock');
    \Config::set('database.connections.sqlsrv_group.password', 'Nz6cU4kAHtnCAFVq');
    \Config::set('database.connections.sqlsrv_group.database', $group->database_name);

    $check_hierarchy = Drivers::where('extended_id', $request->user_id)->noLock()->first();

    $result["rifav_drivers"] = [];

    switch ($check_hierarchy->tipo) {
      case "supervisor":

        $drivers = Drivers::where('id_group_employee', $check_hierarchy->id_group_employee)
                    ->where('id', '!=', $check_hierarchy->id)
                    ->where('id_group_employee', '!=', NULL)->noLock()->get();

        foreach ($drivers as $key_drivers => $value_drivers) {

          $driver_rifav = RifavSummary::join('drivers', 'events_summary_rifav.driverid', '=', 'drivers.extended_id')->where('driverid', $value_drivers->extended_id)
            ->where("startdatetime", ">=", \Carbon\Carbon::parse($request->data_inicio.' '.'00:00:00')->format('Y-m-d H:i:s'))
            ->where("startdatetime", "<=", \Carbon\Carbon::parse($request->data_termino.' '.'23:59:00')->format('Y-m-d H:i:s'))
            ->noLock()
            ->get();

          $result["rifav_drivers"][$value_drivers->extended_id] = [];
          $result["rifav_all_drivers"] = [];
          $result["rifav_drivers"][$value_drivers->extended_id]["driver_name"] = $value_drivers->name;
          $result["rifav_drivers"][$value_drivers->extended_id]["driver_cpf"] = $value_drivers->cpf;

        foreach ($driver_rifav as $key_rifav => $value_rifav) {

            if($value_rifav->word == 'V') {
              if(isset($result["rifav_drivers"][$value_drivers->extended_id]["V"])) {
                $result["rifav_drivers"][$value_drivers->extended_id]["V"] += $value_rifav->totalevent;
              } else {
                $result["rifav_drivers"][$value_drivers->extended_id]["V"] = $value_rifav->totalevent;
              }
            }

            if($value_rifav->word == 'R') {
              if(isset($result["rifav_drivers"][$value_drivers->extended_id]["R"])) {
                $result["rifav_drivers"][$value_drivers->extended_id]["R"] += $value_rifav->totalevent;
              } else {
                $result["rifav_drivers"][$value_drivers->extended_id]["R"] = $value_rifav->totalevent;
              }
            }
            if($value_rifav->word == 'I') {
              if(isset($result["rifav_drivers"][$value_drivers->extended_id]["I"])) {
                $result["rifav_drivers"][$value_drivers->extended_id]["I"] += $value_rifav->totalevent;
              } else {
                $result["rifav_drivers"][$value_drivers->extended_id]["I"] = $value_rifav->totalevent;
              }
            }
            if($value_rifav->word == 'F') {
              if(isset($result["rifav_drivers"][$value_drivers->extended_id]["F"])) {
                $result["rifav_drivers"][$value_drivers->extended_id]["F"] += $value_rifav->totalevent;
              } else {
                $result["rifav_drivers"][$value_drivers->extended_id]["F"] = $value_rifav->totalevent;
              }
            }
            if($value_rifav->word == 'A') {
              if(isset($result["rifav_drivers"][$value_drivers->extended_id]["A"])) {
                $result["rifav_drivers"][$value_drivers->extended_id]["A"] += $value_rifav->totalevent;
              } else {
                $result["rifav_drivers"][$value_drivers->extended_id]["A"] = $value_rifav->totalevent;
              }
            }

          }

          foreach ($result["rifav_drivers"] as $key_rifav_all => $value_rifav_all) {
            foreach (array_keys($result["rifav_drivers"][$key_rifav_all]) as $key => $value) {

              if($value == 'R') {
                if(isset($result["rifav_all_drivers"]["R"])) {
                  $result["rifav_all_drivers"]["R"] += $result["rifav_drivers"][$key_rifav_all][$value];
                } else {
                  $result["rifav_all_drivers"]["R"] = $result["rifav_drivers"][$key_rifav_all][$value];
                }
              }
              if($value == 'I') {
                if(isset($result["rifav_all_drivers"]["I"])) {
                  $result["rifav_all_drivers"]["I"] += $result["rifav_drivers"][$key_rifav_all][$value];
                } else {
                  $result["rifav_all_drivers"]["I"] = $result["rifav_drivers"][$key_rifav_all][$value];
                }
              }
              if($value == 'F') {
                if(isset($result["rifav_all_drivers"]["F"])) {
                  $result["rifav_all_drivers"]["F"] += $result["rifav_drivers"][$key_rifav_all][$value];
                } else {
                  $result["rifav_all_drivers"]["F"] = $result["rifav_drivers"][$key_rifav_all][$value];
                }
              }
              if($value == 'A') {
                if(isset($result["rifav_all_drivers"]["A"])) {
                  $result["rifav_all_drivers"]["A"] += $result["rifav_drivers"][$key_rifav_all][$value];
                } else {
                  $result["rifav_all_drivers"]["A"] = $result["rifav_drivers"][$key_rifav_all][$value];
                }
              }
              if($value == 'V') {
                if(isset($result["rifav_all_drivers"]["V"])) {
                  $result["rifav_all_drivers"]["V"] += $result["rifav_drivers"][$key_rifav_all][$value];
                } else {
                  $result["rifav_all_drivers"]["V"] = $result["rifav_drivers"][$key_rifav_all][$value];
                }
              }
            }
          }

        }

        break;

      case "motorista":

        $driver = Drivers::where('id', $check_hierarchy->id)->noLock()->first();

        $driver_rifav = RifavSummary::where('driverid', $driver->extended_id)
          ->where("startdatetime", ">=", \Carbon\Carbon::parse($request->data_inicio)->format('Y-m-d'))
          ->where("startdatetime", "<=", \Carbon\Carbon::parse($request->data_termino)->format('Y-m-d'))
          ->noLock()
          ->get();

        foreach ($driver_rifav as $key_rifav => $value_rifav) {

          $result["rifav_drivers"][$driver->extended_id]["driver_name"] = $driver->name;
          $result["rifav_drivers"][$driver->extended_id]["driver_cpf"] = $driver->cpf;
          $result["rifav_drivers"][$driver->extended_id]["extended_id"] = $driver->extended_id;

          if($value_rifav->word == 'V') {
            if(isset($result["rifav_drivers"][$driver->extended_id]["V"])) {
              $result["rifav_drivers"][$driver->extended_id]["V"] += $value_rifav->totalevent;
            } else {
              $result["rifav_drivers"][$driver->extended_id]["V"] = $value_rifav->totalevent;
            }
          }

          if($value_rifav->word == 'R') {
            if(isset($result["rifav_drivers"][$driver->extended_id]["R"])) {
              $result["rifav_drivers"][$driver->extended_id]["R"] += $value_rifav->totalevent;
            } else {
              $result["rifav_drivers"][$driver->extended_id]["R"] = $value_rifav->totalevent;
            }
          }
          if($value_rifav->word == 'I') {
            if(isset($result["rifav_drivers"][$driver->extended_id]["I"])) {
              $result["rifav_drivers"][$driver->extended_id]["I"] += $value_rifav->totalevent;
            } else {
              $result["rifav_drivers"][$driver->extended_id]["I"] = $value_rifav->totalevent;
            }
          }
          if($value_rifav->word == 'F') {
            if(isset($result["rifav_drivers"][$driver->extended_id]["F"])) {
              $result["rifav_drivers"][$driver->extended_id]["F"] += $value_rifav->totalevent;
            } else {
              $result["rifav_drivers"][$driver->extended_id]["F"] = $value_rifav->totalevent;
            }
          }
          if($value_rifav->word == 'A') {
            if(isset($result["rifav_drivers"][$driver->extended_id]["A"])) {
              $result["rifav_drivers"][$driver->extended_id]["A"] += $value_rifav->totalevent;
            } else {
              $result["rifav_drivers"][$driver->extended_id]["A"] = $value_rifav->totalevent;
            }
          }

        }

        break;

      default:

        break;
    }

    return $result;
  }

  public function getTotalRifavByDriverAndRangeDateNew(Request $request)
  {
    $group = Group::where('id', $request->matricula)->first();

    \Config::set('database.connections.sqlsrv_group.host', 'blustock.database.windows.net');
    \Config::set('database.connections.sqlsrv_group.username', 'AdminBluStock');
    \Config::set('database.connections.sqlsrv_group.password', 'Nz6cU4kAHtnCAFVq');
    \Config::set('database.connections.sqlsrv_group.database', $group->database_name);

    $check_hierarchy = Drivers::where('extended_id', $request->user_id)->noLock()->first();

    $result["rifav_drivers"] = [];

    if($request->order_by_name === 'DESC'){
      $order_by = 'DESC';
    } else {
      $order_by = 'ASC';
    }

    switch ($check_hierarchy->tipo) {
      case "supervisor":

      if(isset($request->is_search)) {

        $drivers =
        Drivers::leftJoin('events_summary_rifav', 'events_summary_rifav.driverid', '=', 'drivers.extended_id')
          ->select('drivers.name', 'events_summary_rifav.driverid', 'drivers.cpf', 'drivers.extended_id', 'events_summary_rifav.word', \DB::raw('SUM(events_summary_rifav.totalevent) as total'))
          ->where('id_group_employee', $check_hierarchy->id_group_employee)
          ->where('id', '!=', $check_hierarchy->id)
          ->where('id_group_employee', '!=', NULL)
          ->where('name', 'like', '%'.$request->is_search.'%')
          ->where("events_summary_rifav.startdatetime", ">=", \Carbon\Carbon::parse($request->data_inicio.' '.'00:00:00')->format('Y-m-d H:i:s'))
          ->where("events_summary_rifav.startdatetime", "<=", \Carbon\Carbon::parse($request->data_termino.' '.'23:59:00')->format('Y-m-d H:i:s'))
          ->groupBy('drivers.name', 'drivers.cpf', 'events_summary_rifav.driverid', 'events_summary_rifav.word', 'drivers.extended_id')
          ->orderBy('name', $order_by)
          ->noLock()
          ->get();


      } else {

        $drivers =
        Drivers::leftJoin('events_summary_rifav', 'events_summary_rifav.driverid', '=', 'drivers.extended_id')
          ->select('drivers.name', 'events_summary_rifav.driverid', 'drivers.cpf', 'drivers.extended_id', 'events_summary_rifav.word', \DB::raw('SUM(events_summary_rifav.totalevent) as total'))
          ->where('id', '!=', $check_hierarchy->id)
          ->where('drivers.deleted_at', NULL)
          ->where('id_group_employee', '!=', NULL)
          ->where("events_summary_rifav.startdatetime", ">=", \Carbon\Carbon::parse($request->data_inicio.' '.'00:00:00')->format('Y-m-d H:i:s'))
          ->where("events_summary_rifav.startdatetime", "<=", \Carbon\Carbon::parse($request->data_termino.' '.'23:59:00')->format('Y-m-d H:i:s'))
          ->groupBy('drivers.name', 'drivers.cpf', 'events_summary_rifav.driverid', 'events_summary_rifav.word', 'drivers.extended_id')
          ->orderBy('name', $order_by)
          ->noLock()
          ->get();

      }

        foreach ($drivers as $key_drivers => $value_drivers) {

          if(isset($result["rifav_drivers"][$value_drivers->driverid])) {

          } else {
            $result["rifav_drivers"][$value_drivers->driverid] = [];
            $result["rifav_drivers"][$value_drivers->driverid]["R"] = 0;
            $result["rifav_drivers"][$value_drivers->driverid]["I"] = 0;
            $result["rifav_drivers"][$value_drivers->driverid]["F"] = 0;
            $result["rifav_drivers"][$value_drivers->driverid]["A"] = 0;
            $result["rifav_drivers"][$value_drivers->driverid]["V"] = 0;
          }

          $result["rifav_drivers"][$value_drivers->driverid]["driver_name"] = $value_drivers->name;
          $result["rifav_drivers"][$value_drivers->driverid]["driver_cpf"] = $value_drivers->cpf;
          $result["rifav_drivers"][$value_drivers->driverid]["extended_id"] = $value_drivers->extended_id;

          if(isset($result["rifav_drivers"][$value_drivers->driverid]["total"])) {
            $result["rifav_drivers"][$value_drivers->driverid]["total"] += $value_drivers->total;
          } else {
            $result["rifav_drivers"][$value_drivers->driverid]["total"] = $value_drivers->total;
          }

          if($drivers[$key_drivers]["word"] == 'R') {
            if(isset($result["rifav_drivers"][$value_drivers->driverid]["R"])) {
              $result["rifav_drivers"][$value_drivers->driverid]["R"] += $value_drivers->total;
            } else {
              $result["rifav_drivers"][$value_drivers->driverid]["R"] = $value_drivers->total;
            }
          }

          if($drivers[$key_drivers]["word"] == 'I') {
            if(isset($result["rifav_drivers"][$value_drivers->driverid]["I"])) {
              $result["rifav_drivers"][$value_drivers->driverid]["I"] += $value_drivers->total;
            } else {
              $result["rifav_drivers"][$value_drivers->driverid]["I"] = $value_drivers->total;
            }
          }
          if($value_drivers->word == 'F') {
            if(isset($result["rifav_drivers"][$value_drivers->driverid]["F"])) {
              $result["rifav_drivers"][$value_drivers->driverid]["F"] += $value_drivers->total;
            } else {
              $result["rifav_drivers"][$value_drivers->driverid]["F"] = $value_drivers->total;
            }
          }
          if($value_drivers->word == 'A') {
            if(isset($result["rifav_drivers"][$value_drivers->driverid]["A"])) {
              $result["rifav_drivers"][$value_drivers->driverid]["A"] += $value_drivers->total;
            } else {
              $result["rifav_drivers"][$value_drivers->driverid]["A"] = $value_drivers->total;
            }
          }
          if($value_drivers->word == 'V') {
            if(isset($result["rifav_drivers"][$value_drivers->driverid]["V"])) {
              $result["rifav_drivers"][$value_drivers->driverid]["V"] += $value_drivers->total;
            } else {
              $result["rifav_drivers"][$value_drivers->driverid]["V"] = $value_drivers->total;
            }
          }
        }

        if(isset($request->order_by_events) && $request->order_by_events === 'DESC'){
          usort($result["rifav_drivers"], function($a, $b) {
            return $a["total"] < $b["total"] ? 1 : -1;
          });
        }

        if(isset($request->order_by_events) && $request->order_by_events === 'ASC'){
          usort($result["rifav_drivers"], function($a, $b) {
            return $a["total"] > $b["total"] ? 1 : -1;
          });
        }

        $perPage = 30;

        $currentPage = \Illuminate\Pagination\LengthAwarePaginator::resolveCurrentPage();

        $itemCollection = $result["rifav_drivers"];

        $currentPageItems = array_slice($itemCollection,($currentPage * $perPage) - $perPage, $perPage);

        $paginator = new \Illuminate\Pagination\LengthAwarePaginator($currentPageItems, count($itemCollection), $perPage);

        $result["rifav_drivers"] = $paginator;

      break;

      case "motorista":

      $drivers =
        Drivers::leftJoin('events_summary_rifav', 'events_summary_rifav.driverid', '=', 'drivers.extended_id')
          ->select(
            'drivers.name',
            'events_summary_rifav.driverid',
            'drivers.cpf',
            'events_summary_rifav.word',
            \DB::raw('SUM(events_summary_rifav.totalevent) as total')
          )
          ->where('id', $check_hierarchy->id)
          ->where('name', 'like', '%'.$request->is_search.'%')
          ->where("events_summary_rifav.startdatetime", ">=", \Carbon\Carbon::parse($request->data_inicio.' '.'00:00:00')->format('Y-m-d H:i:s'))
          ->where("events_summary_rifav.startdatetime", "<=", \Carbon\Carbon::parse($request->data_termino.' '.'23:59:59')->format('Y-m-d H:i:s'))
          ->groupBy('drivers.name', 'drivers.cpf', 'events_summary_rifav.driverid', 'events_summary_rifav.word')
          ->orderBy('total', $order_by)
          ->noLock()
          ->get();

        $value_drivers["driverid"] = [];

        foreach ($drivers as $key_drivers => $value_drivers) {

          if(isset($result["rifav_drivers"][$value_drivers->driverid])) {

          } else {
            $result["rifav_drivers"][$value_drivers->driverid] = [];
          }

          $result["rifav_drivers"][$value_drivers->driverid]["driver_name"] = $value_drivers->name;
          $result["rifav_drivers"][$value_drivers->driverid]["driver_cpf"] = $value_drivers->cpf;
          $result["rifav_drivers"][$value_drivers->driverid]["extended_id"] = $value_drivers->extended_id;

          if(isset($result["rifav_drivers"][$value_drivers->driverid]["total"])) {
            $result["rifav_drivers"][$value_drivers->driverid]["total"] += $value_drivers->total;
          } else {
            $result["rifav_drivers"][$value_drivers->driverid]["total"] = $value_drivers->total;
          }

          if($drivers[$key_drivers]["word"] == 'R') {
            if(isset($result["rifav_drivers"][$value_drivers->driverid]["R"])) {
              $result["rifav_drivers"][$value_drivers->driverid]["R"] += $value_drivers->total;
            } else {
              $result["rifav_drivers"][$value_drivers->driverid]["R"] = $value_drivers->total;
            }
          }

          if($drivers[$key_drivers]["word"] == 'I') {
            if(isset($result["rifav_drivers"][$value_drivers->driverid]["I"])) {
              $result["rifav_drivers"][$value_drivers->driverid]["I"] += $value_drivers->total;
            } else {
              $result["rifav_drivers"][$value_drivers->driverid]["I"] = $value_drivers->total;
            }
          }
          if($value_drivers->word == 'F') {
            if(isset($result["rifav_drivers"][$value_drivers->driverid]["F"])) {
              $result["rifav_drivers"][$value_drivers->driverid]["F"] += $value_drivers->total;
            } else {
              $result["rifav_drivers"][$value_drivers->driverid]["F"] = $value_drivers->total;
            }
          }
          if($value_drivers->word == 'A') {
            if(isset($result["rifav_drivers"][$value_drivers->driverid]["A"])) {
              $result["rifav_drivers"][$value_drivers->driverid]["A"] += $value_drivers->total;
            } else {
              $result["rifav_drivers"][$value_drivers->driverid]["A"] = $value_drivers->total;
            }
          }
          if($value_drivers->word == 'V') {
            if(isset($result["rifav_drivers"][$value_drivers->driverid]["V"])) {
              $result["rifav_drivers"][$value_drivers->driverid]["V"] += $value_drivers->total;
            } else {
              $result["rifav_drivers"][$value_drivers->driverid]["V"] = $value_drivers->total;
            }
          }

        }

        $assets =
            Assets::leftJoin('events_summary_rifav_asset', 'events_summary_rifav_asset.assetid', '=', 'assets.id')
              ->select(
                'assets.description',
                'assets.id',
                'assets.id_unit',
                'events_summary_rifav_asset.word',
                \DB::raw('SUM(events_summary_rifav_asset.totalevent) as total')
              )
              ->where('events_summary_rifav_asset.driverid', $check_hierarchy->extended_id)
              ->where("events_summary_rifav_asset.startdatetime", ">=", \Carbon\Carbon::parse($request->data_inicio.' '.'00:00:00')->format('Y-m-d H:i:s'))
              ->where("events_summary_rifav_asset.startdatetime", "<=", \Carbon\Carbon::parse($request->data_termino.' '.'23:59:59')->format('Y-m-d H:i:s'))
              ->groupBy('assets.description', 'assets.id', 'assets.id_unit', 'events_summary_rifav_asset.word')
              ->orderBy('total', $order_by)
              ->get();

          foreach ($assets as $key_assets => $value_assets) {

            if(isset($result["rifav_drivers"][$check_hierarchy->extended_id]["assets"][$value_assets->description])) {

            } else {
              $result["rifav_drivers"][$check_hierarchy->extended_id]["assets"][$value_assets->description] = [];
            }

            $result["rifav_drivers"][$check_hierarchy->extended_id]["assets"][$value_assets->description]["description"] = $value_assets->description;
            $result["rifav_drivers"][$check_hierarchy->extended_id]["assets"][$value_assets->description]["id"] = $value_assets->id;
            $result["rifav_drivers"][$check_hierarchy->extended_id]["assets"][$value_assets->description]["id_unit"] = $value_assets->id_unit;

            if(isset($result["rifav_drivers"][$check_hierarchy->extended_id]["assets"][$value_assets->description]["total"])) {
              $result["rifav_drivers"][$check_hierarchy->extended_id]["assets"][$value_assets->description]["total"] += $value_assets->total;
            } else {
              $result["rifav_drivers"][$check_hierarchy->extended_id]["assets"][$value_assets->description]["total"] = $value_assets->total;
            }

            if($assets[$key_assets]["word"] == 'R') {
              if(isset($result["rifav_drivers"][$check_hierarchy->extended_id]["assets"][$value_assets->description]["R"])) {
                $result["rifav_drivers"][$check_hierarchy->extended_id]["assets"][$value_assets->description]["R"] += $value_assets->total;
              } else {
                $result["rifav_drivers"][$check_hierarchy->extended_id]["assets"][$value_assets->description]["R"] = $value_assets->total;
              }
            }

            if($assets[$key_assets]["word"] == 'I') {
              if(isset($result["rifav_drivers"][$check_hierarchy->extended_id]["assets"][$value_assets->description]["I"])) {
                $result["rifav_drivers"][$check_hierarchy->extended_id]["assets"][$value_assets->description]["I"] += $value_assets->total;
              } else {
                $result["rifav_drivers"][$check_hierarchy->extended_id]["assets"][$value_assets->description]["I"] = $value_assets->total;
              }
            }
            if($value_assets->word == 'F') {
              if(isset($result["rifav_drivers"][$check_hierarchy->extended_id]["assets"][$value_assets->description]["F"])) {
                $result["rifav_drivers"][$check_hierarchy->extended_id]["assets"][$value_assets->description]["F"] += $value_assets->total;
              } else {
                $result["rifav_drivers"][$check_hierarchy->extended_id]["assets"][$value_assets->description]["F"] = $value_assets->total;
              }
            }
            if($value_assets->word == 'A') {
              if(isset($result["rifav_drivers"][$check_hierarchy->extended_id]["assets"][$value_assets->description]["A"])) {
                $result["rifav_drivers"][$check_hierarchy->extended_id]["assets"][$value_assets->description]["A"] += $value_assets->total;
              } else {
                $result["rifav_drivers"][$check_hierarchy->extended_id]["assets"][$value_assets->description]["A"] = $value_assets->total;
              }
            }
            if($value_assets->word == 'V') {
              if(isset($result["rifav_drivers"][$check_hierarchy->extended_id]["assets"][$value_assets->description]["V"])) {
                $result["rifav_drivers"][$check_hierarchy->extended_id]["assets"][$value_assets->description]["V"] += $value_assets->total;
              } else {
                $result["rifav_drivers"][$check_hierarchy->extended_id]["assets"][$value_assets->description]["V"] = $value_assets->total;
              }
            }

          }

        break;

      default:

        break;
    }

    return $result;
  }

  public function getTotalRifavByAssetAndRangeDateNew(Request $request)
  {
    $group = Group::where('id', $request->matricula)->first();

    \Config::set('database.connections.sqlsrv_group.host', 'blustock.database.windows.net');
    \Config::set('database.connections.sqlsrv_group.username', 'AdminBluStock');
    \Config::set('database.connections.sqlsrv_group.password', 'Nz6cU4kAHtnCAFVq');
    \Config::set('database.connections.sqlsrv_group.database', $group->database_name);

    $check_hierarchy = Drivers::where('extended_id', $request->user_id)->where('deleted_at', NULL)->noLock()->first();

    $result["rifav_drivers"] = [];

    if($request->order_by_name === 'DESC'){
      $order_by = 'DESC';
    } else {
      $order_by = 'ASC';
    }

    switch ($check_hierarchy->tipo) {
      case "supervisor":

      if(isset($request->is_search)) {

       $drivers =
          Drivers::select('extended_id')
            ->where('id_group_employee', $check_hierarchy->id_group_employee)
            ->where('id', '!=', $check_hierarchy->id)
            ->where('id_group_employee', '!=', NULL)
            ->where('deleted_at', NULL)
            ->groupBy('extended_id')
            ->pluck('extended_id');

        $assets =
        Assets::leftJoin('events_summary_rifav_asset', 'events_summary_rifav_asset.assetid', '=', 'assets.id')
          ->select(
            'assets.description',
            'assets.id',
            'events_summary_rifav_asset.word',
            \DB::raw('SUM(events_summary_rifav_asset.totalevent) as total')
          )
          ->where("events_summary_rifav_asset.startdatetime", "=", $request->data_inicio)
          ->where('assets.description', 'like', '%'.$request->is_search.'%')
          ->groupBy('assets.description', 'assets.id', 'events_summary_rifav_asset.word')
          ->orderBy('total', $order_by)
          ->get();

      } else {

      $drivers =
        Drivers::select('extended_id')
          ->where('id_group_employee', $check_hierarchy->id_group_employee)
          ->where('id', '!=', $check_hierarchy->id)
          ->where('deleted_at', NULL)
          ->where('id_group_employee', '!=', NULL)
          ->groupBy('extended_id')
          ->pluck('extended_id');

      $assets =
        Assets::leftJoin('events_summary_rifav_asset', 'events_summary_rifav_asset.assetid', '=', 'assets.id')
          ->select(
            'assets.description',
            'assets.id',
            'assets.id_unit',
            'events_summary_rifav_asset.word',
            \DB::raw('SUM(events_summary_rifav_asset.totalevent) as total')
          )
          ->where("events_summary_rifav_asset.startdatetime", "=", $request->data_inicio)
          ->groupBy('assets.description', 'assets.id', 'assets.id_unit', 'events_summary_rifav_asset.word')
          ->orderBy('total', $order_by)
          ->get();

      }

        foreach ($assets as $key_assets => $value_assets) {

          if(isset($result["rifav_drivers"][$value_assets->description])) {

          } else {
            $result["rifav_drivers"][$value_assets->description] = [];
          }

          $result["rifav_drivers"][$value_assets->description]["description"] = $value_assets->description;
          $result["rifav_drivers"][$value_assets->description]["id"] = $value_assets->id;
          $result["rifav_drivers"][$value_assets->description]["id_unit"] = $value_assets->id_unit;

          if(isset($result["rifav_drivers"][$value_assets->description]["total"])) {
            $result["rifav_drivers"][$value_assets->description]["total"] += $value_assets->total;
          } else {
            $result["rifav_drivers"][$value_assets->description]["total"] = $value_assets->total;
          }

          if($assets[$key_assets]["word"] == 'R') {
            if(isset($result["rifav_drivers"][$value_assets->description]["R"])) {
              $result["rifav_drivers"][$value_assets->description]["R"] += $value_assets->total;
            } else {
              $result["rifav_drivers"][$value_assets->description]["R"] = $value_assets->total;
            }
          }

          if($assets[$key_assets]["word"] == 'I') {
            if(isset($result["rifav_drivers"][$value_assets->description]["I"])) {
              $result["rifav_drivers"][$value_assets->description]["I"] += $value_assets->total;
            } else {
              $result["rifav_drivers"][$value_assets->description]["I"] = $value_assets->total;
            }
          }
          if($value_assets->word == 'F') {
            if(isset($result["rifav_drivers"][$value_assets->description]["F"])) {
              $result["rifav_drivers"][$value_assets->description]["F"] += $value_assets->total;
            } else {
              $result["rifav_drivers"][$value_assets->description]["F"] = $value_assets->total;
            }
          }
          if($value_assets->word == 'A') {
            if(isset($result["rifav_drivers"][$value_assets->description]["A"])) {
              $result["rifav_drivers"][$value_assets->description]["A"] += $value_assets->total;
            } else {
              $result["rifav_drivers"][$value_assets->description]["A"] = $value_assets->total;
            }
          }
          if($value_assets->word == 'V') {
            if(isset($result["rifav_drivers"][$value_assets->description]["V"])) {
              $result["rifav_drivers"][$value_assets->description]["V"] += $value_assets->total;
            } else {
              $result["rifav_drivers"][$value_assets->description]["V"] = $value_assets->total;
            }
          }

        }

        if(isset($request->order_by_events) && $request->order_by_events === 'DESC'){
          usort($result["rifav_drivers"], function($a, $b) {
            return $a["total"] < $b["total"] ? 1 : -1;
          });
        }

        if(isset($request->order_by_events) && $request->order_by_events === 'ASC'){
          usort($result["rifav_drivers"], function($a, $b) {
            return $a["total"] > $b["total"] ? 1 : -1;
          });
        }

        $perPage = 30;

        $currentPage = \Illuminate\Pagination\LengthAwarePaginator::resolveCurrentPage();

        $itemCollection = $result["rifav_drivers"];

        $currentPageItems = array_slice($itemCollection,($currentPage * $perPage) - $perPage, $perPage);

        $paginator = new \Illuminate\Pagination\LengthAwarePaginator($currentPageItems, count($itemCollection), $perPage);

        $result["rifav_drivers"] = $paginator;

      break;

      default:

        break;
    }

    return $result;
  }

  public function getWordRifavByDriverAndRangeDate(Request $request)
  {
    $group = Group::where('id', $request->matricula)->first();

    \Config::set('database.connections.sqlsrv_group.host', 'blustock.database.windows.net');
    \Config::set('database.connections.sqlsrv_group.username', 'AdminBluStock');
    \Config::set('database.connections.sqlsrv_group.password', 'Nz6cU4kAHtnCAFVq');
    \Config::set('database.connections.sqlsrv_group.database', $group->database_name);

    $check_hierarchy = Drivers::where('extended_id', $request->driver_id)->noLock()->first();

    $result["rifav_drivers"] = [];

    switch ($check_hierarchy->tipo) {
      case "supervisor":

       $print = EventsRifav::selectRaw("le.description, COUNT(*)")
          ->join('LibraryEvents as le', 'le.eventid', '=', 'events_rifav.libraryeventid')
          ->join('drivers as moto', 'moto.extended_id', '=', 'events_rifav.driverid')
          ->join('grupos_employees as ges', 'ges.id', '=', 'moto.id_group_employee')
          ->join('drivers as super', 'super.id', '=', 'ges.id_supervisor')
          ->where("events_rifav.startdatetime", ">=", $request->data_inicio)
          ->where("events_rifav.startdatetime", "<=", $request->data_termino)
          ->where("events_rifav.word", $request->word)
          ->where('super.id', $check_hierarchy->id)
          ->where('moto.id_group_employee', $check_hierarchy->id_group_employee)
          ->groupBy('le.description')
          ->get();

          $result["rifav_drivers"] = $print;

      break;

      case "motorista":

        $eventsByWord = EventsRifav::selectRaw("le.description, COUNT(*) as total")
          ->join('LibraryEvents as le', 'le.eventid', '=', 'events_rifav.libraryeventid')
          ->where("events_rifav.word", strtoupper($request->word))
          ->where("events_rifav.startdatetime", ">=", $request->data_inicio)
          ->where("events_rifav.startdatetime", "<=", $request->data_termino)
          ->where("events_rifav.driverid", "=", $request->driver_id)
          ->groupBy('le.description')
          ->get();

        foreach ($eventsByWord as $value_words) {

          if(isset($result["rifav_drivers"][$value_words->description])) {

          } else {
            $result["rifav_drivers"][$value_words->description] = [];
          }

          if(isset($result["rifav_drivers"][$value_words->description]["total"])) {
            $result["rifav_drivers"][$value_words->description]["total"] += $value_words->total;

          } else {
            $result["rifav_drivers"][$value_words->description]["total"] = $value_words->total;
          }

        }

      break;

      default:

        break;
    }

    return $result;
  }

  public function getWordRifavByAssetAndRangeDate(Request $request)
  {
    $group = Group::where('id', $request->matricula)->first();

    \Config::set('database.connections.sqlsrv_group.host', 'blustock.database.windows.net');
    \Config::set('database.connections.sqlsrv_group.username', 'AdminBluStock');
    \Config::set('database.connections.sqlsrv_group.password', 'Nz6cU4kAHtnCAFVq');
    \Config::set('database.connections.sqlsrv_group.database', $group->database_name);

    $check_hierarchy = Drivers::where('extended_id', $request->driver_id)->first();

    $result["rifav_drivers"] = [];

    switch ($check_hierarchy->tipo) {
      case "supervisor":

        if(isset($request->asset_id) && $request->asset_id != '') {

          $eventsByWord = EventsRifav::selectRaw("le.description, COUNT(*) as total")
          ->join('LibraryEvents as le', 'le.eventid', '=', 'events_rifav.libraryeventid')
          ->where("events_rifav.word", strtoupper($request->word))
          ->where("events_rifav.startdatetime", "=", $request->data_inicio)
          ->where("events_rifav.assetid", "=", $request->asset_id)
          ->groupBy('le.description')
          ->get();

          foreach ($eventsByWord as $value_words) {

            if(isset($result["rifav_drivers"][$value_words->description])) {

            } else {
              $result["rifav_drivers"][$value_words->description] = [];
            }

            if(isset($result["rifav_drivers"][$value_words->description]["total"])) {
              $result["rifav_drivers"][$value_words->description]["total"] += $value_words->total;

            } else {
              $result["rifav_drivers"][$value_words->description]["total"] = $value_words->total;
            }

          }

        } else {
          $print = EventsRifav::selectRaw("le.description, COUNT(*)")
          ->join('LibraryEvents as le', 'le.eventid', '=', 'events_rifav.libraryeventid')
          ->join('drivers as moto', 'moto.extended_id', '=', 'events_rifav.driverid')
          ->join('grupos_employees as ges', 'ges.id', '=', 'moto.id_group_employee')
          ->join('drivers as super', 'super.id', '=', 'ges.id_supervisor')
          ->where("events_rifav.startdatetime", "=", $request->data_inicio)
          ->where("events_rifav.word", $request->word)
          ->where('super.id', $check_hierarchy->id)
          ->where('moto.id_group_employee', $check_hierarchy->id_group_employee)
          ->groupBy('le.description')
          ->get();
        }

        // $print = EventsRifav::selectRaw("le.description, COUNT(*)")
        //   ->join('LibraryEvents as le', 'le.eventid', '=', 'events_rifav.libraryeventid')
        //   ->join('drivers as moto', 'moto.extended_id', '=', 'events_rifav.driverid')
        //   ->join('grupos_employees as ges', 'ges.id', '=', 'moto.id_group_employee')
        //   ->join('drivers as super', 'super.id', '=', 'ges.id_supervisor')
        //   ->where("events_rifav.startdatetime", ">=", \Carbon\Carbon::parse($request->data_inicio.' '.'00:00:00')->format('Y-m-d H:i:s'))
        //   ->where("events_rifav.startdatetime", "<=", \Carbon\Carbon::parse($request->data_termino.' '.'23:59:00')->format('Y-m-d H:i:s'))
        //   ->where("events_rifav.word", $request->word)
        //   ->where('super.id', $check_hierarchy->id)
        //   ->where('moto.id_group_employee', $check_hierarchy->id_group_employee)
        //   ->groupBy('le.description')
        //   ->get();

          // $result["rifav_drivers"] = $print;

      break;

      case "motorista":
        $eventsByWord = EventsRifav::selectRaw("le.description, COUNT(*) as total")
        ->join('LibraryEvents as le', 'le.eventid', '=', 'events_rifav.libraryeventid')
        ->where("events_rifav.word", strtoupper($request->word))
        ->where("events_rifav.startdatetime", "=", $request->data_inicio)
        ->where("events_rifav.assetid", "=", $request->asset_id)
        ->groupBy('le.description')
        ->get();

      foreach ($eventsByWord as $value_words) {

        if(isset($result["rifav_drivers"][$value_words->description])) {

        } else {
          $result["rifav_drivers"][$value_words->description] = [];
        }

        if(isset($result["rifav_drivers"][$value_words->description]["total"])) {
          $result["rifav_drivers"][$value_words->description]["total"] += $value_words->total;

        } else {
          $result["rifav_drivers"][$value_words->description]["total"] = $value_words->total;
        }

      }

      break;

      default:

        break;
    }

    return $result;
  }

  public function getTotalRifavByDriverAndRangeDateGraph(Request $request)
  {
    $group = Group::where('id', $request->matricula)->first();

    \Config::set('database.connections.sqlsrv_group.host', 'blustock.database.windows.net');
    \Config::set('database.connections.sqlsrv_group.username', 'AdminBluStock');
    \Config::set('database.connections.sqlsrv_group.password', 'Nz6cU4kAHtnCAFVq');
    \Config::set('database.connections.sqlsrv_group.database', $group->database_name);

    $check_hierarchy = Drivers::where('extended_id', $request->user_id)->first();

    $result["rifav_all_drivers"] = [];

    switch ($check_hierarchy->tipo) {
      case "supervisor":

      $drivers =
      Drivers::leftJoin('events_summary_rifav', 'events_summary_rifav.driverid', '=', 'drivers.extended_id')
        ->select('events_summary_rifav.word', \DB::raw('SUM(events_summary_rifav.totalevent) as total'))
        ->where('id_group_employee', $check_hierarchy->id_group_employee)
        ->where('id', '!=', $check_hierarchy->id)
        ->where("events_summary_rifav.startdatetime", ">=", \Carbon\Carbon::parse($request->data_inicio)->format('Y-m-d'))
        ->where("events_summary_rifav.startdatetime", "<=", \Carbon\Carbon::parse($request->data_termino)->format('Y-m-d'))
        ->groupBy('events_summary_rifav.word')
        ->noLock()
        ->get();

        foreach ($drivers as $key_drivers => $value_drivers) {

          if($drivers[$key_drivers]["word"] === 'R') {
            if(isset($result["rifav_all_drivers"]["R"])) {
              $result["rifav_all_drivers"]["R"] += $drivers[$key_drivers]->total;
            } else {
              $result["rifav_all_drivers"]["R"] = $drivers[$key_drivers]->total;
            }
          }

          if($drivers[$key_drivers]["word"] === 'I') {
            if(isset($result["rifav_all_drivers"]["I"])) {
              $result["rifav_all_drivers"]["I"] += $drivers[$key_drivers]->total;
            } else {
              $result["rifav_all_drivers"]["I"] = $drivers[$key_drivers]->total;
            }
          }

          if($drivers[$key_drivers]["word"] === 'F') {
            if(isset($result["rifav_all_drivers"]["F"])) {
              $result["rifav_all_drivers"]["F"] += $drivers[$key_drivers]->total;
            } else {
              $result["rifav_all_drivers"]["F"] = $drivers[$key_drivers]->total;
            }
          }

          if($drivers[$key_drivers]["word"] === 'A') {
            if(isset($result["rifav_all_drivers"]["A"])) {
              $result["rifav_all_drivers"]["A"] += $drivers[$key_drivers]->total;
            } else {
              $result["rifav_all_drivers"]["A"] = $drivers[$key_drivers]->total;
            }
          }

          if($drivers[$key_drivers]["word"] === 'V') {
            if(isset($result["rifav_all_drivers"]["V"])) {
              $result["rifav_all_drivers"]["V"] += $drivers[$key_drivers]->total;
            } else {
              $result["rifav_all_drivers"]["V"] = $drivers[$key_drivers]->total;
            }
          }

        }

        break;

      default:

        break;
    }

    return $result;
  }

  public function profileUpdate(Request $request)
  {
    $group = Group::where('id', $request->matricula)->first();

    \Config::set('database.connections.sqlsrv_group.host', 'blustock.database.windows.net');
    \Config::set('database.connections.sqlsrv_group.username', 'AdminBluStock');
    \Config::set('database.connections.sqlsrv_group.password', 'Nz6cU4kAHtnCAFVq');
    \Config::set('database.connections.sqlsrv_group.database', $group->database_name);

    $check_hierarchy = Drivers::where('extended_id', $request->user_id)->first();

    switch ($check_hierarchy->tipo) {
      case "supervisor":

        break;

      default:

        break;
    }

    return $request->file;
  }

  public function getDetalhesViagemByAsset(Request $request)
  {

    $group = Group::where('id', $request->matricula)->first();

    \Config::set('database.connections.sqlsrv_group.host', 'blustock.database.windows.net');
    \Config::set('database.connections.sqlsrv_group.username', 'AdminBluStock');
    \Config::set('database.connections.sqlsrv_group.password', 'Nz6cU4kAHtnCAFVq');
    \Config::set('database.connections.sqlsrv_group.database', $group->database_name);

    $check_hierarchy = Drivers::where('extended_id', $request->user_id)->first();

    $check_grupo_rifav = RifavoParams::where('group_id', $request->matricula)->first();

    $start = $request->data_inicio;
    $end = $request->data_termino;

    $params = \App\Models\Group::on('sqlsrv')->join('fuso_list', 'fuso_list.id', '=', 'groups.idfuso')->where('groups.id', $request->matricula)->first();

    $trips_array = [];

    // $getTripsByAssetAndDate = \App\Models\Trips::on('sqlsrv_group')
    //   ->join('events_rifav', 'events_rifav.unitid', '=', 'Trips.UnitID')
    //   ->join('LibraryEvents', 'LibraryEvents.EventId', '=', 'events_rifav.libraryeventid')
    //   ->where('Trips.UnitId', $request->asset_id)
    //   ->where('Trips.DriverId', $request->user_id)
    //   ->where('events_rifav.driverid', $request->user_id)
    //   ->where('events_rifav.unitid', $request->asset_id)
    //   ->where('Trips.TripStart', '<=', \Carbon\Carbon::parse($request->data_termino.' 23:59:00')->format('Y-m-d H:i:s'))
    //   ->where('Trips.TripStart', '>=', \Carbon\Carbon::parse($request->data_inicio.' 00:00:00')->format('Y-m-d H:i:s'))
    //   ->where('events_rifav.startdatetime', '<=', \Carbon\Carbon::parse($request->data_termino.' 23:59:00')->format('Y-m-d H:i:s'))
    //   ->where('events_rifav.startdatetime', '>=', \Carbon\Carbon::parse($request->data_inicio.' 00:00:00')->format('Y-m-d H:i:s'))
    //   ->with('driver')
    //   ->with('assets')
    //   ->lock('WITH(NOLOCK)')
    //   ->orderBy(\DB::raw('CAST(Trips.TripStart as datetime2)', 'ASC'))
    //   ->get();

    switch ($check_hierarchy->tipo) {

      case "supervisor":

        if(isset($request->driver_id)) {

          $getTripsByAssetAndDate = \App\Models\Trips::on('sqlsrv_group')
            ->selectRaw("*, TripStart AT TIME ZONE 'UTC' AT TIME ZONE '$params->sql_fuso_name' AS TripStart, TripEnd AT TIME ZONE 'UTC' AT TIME ZONE '$params->sql_fuso_name' AS TripEnd")
            ->where('Trips.DriverId', $request->driver_id)
            ->where('Trips.TripStart', '<=', \Carbon\Carbon::parse($request->data_termino.' 23:59:59')->format('Y-m-d H:i:s'))
            ->where('Trips.TripStart', '>=', \Carbon\Carbon::parse($request->data_inicio.' 00:00:00')->format('Y-m-d H:i:s'))
            ->with('driver')
            ->with('assets')
            ->lock('WITH(NOLOCK)')
            ->orderBy(\DB::raw('CAST(Trips.TripStart as datetime2)', 'ASC'))
            ->get();

          foreach ($getTripsByAssetAndDate as $index => $value) {

            $getTripsByAssetAndDate[$index]['rifav'] = Events::join('LibraryEvents', 'LibraryEvents.EventId', '=', 'Events.EventId')
              ->selectRaw("*, StartDateTime AT TIME ZONE 'UTC' AT TIME ZONE '$params->sql_fuso_name' AS StartDateTime, EndDateTime AT TIME ZONE 'UTC' AT TIME ZONE '$params->sql_fuso_name' AS EndDateTime")
              // ->where('Events.UnitId', $request->asset_id)
              ->where('Events.DriverId', $request->driver_id)
              ->where('Events.EndDateTime', '<=', \Carbon\Carbon::parse($getTripsByAssetAndDate[$index]["TripEnd"])->timezone('UTC')->format('Y-m-d H:i:s'))
              ->where('Events.StartDateTime', '>=', \Carbon\Carbon::parse($getTripsByAssetAndDate[$index]["TripStart"])->timezone('UTC')->format('Y-m-d H:i:s'))
              ->whereNotIn('Events.EventId', [18, 17, 20, 19, 36, 37, 38, 39])
              ->get();

            foreach ($getTripsByAssetAndDate[$index]['rifav'] as $key => $item) {

              $getTripsByAssetAndDate[$index]['rifav'][$key]->rifavo = '';

              if(isset($check_grupo_rifav->grupo_r) && in_array($item->EventId, explode(',', $check_grupo_rifav->grupo_r))) {
                $getTripsByAssetAndDate[$index]['rifav'][$key]->rifavo = 'r';
              }

              if(isset($check_grupo_rifav->grupo_i) && in_array($item->EventId, explode(',', $check_grupo_rifav->grupo_i))) {
                $getTripsByAssetAndDate[$index]['rifav'][$key]->rifavo = 'i';
              }

              if(isset($check_grupo_rifav->grupo_f) && in_array($item->EventId, explode(',', $check_grupo_rifav->grupo_f))) {
                $getTripsByAssetAndDate[$index]['rifav'][$key]->rifavo = 'f';
              }

              if(isset($check_grupo_rifav->grupo_a) && in_array($item->EventId, explode(',', $check_grupo_rifav->grupo_a))) {
                $getTripsByAssetAndDate[$index]['rifav'][$key]->rifavo = 'a';
              }

              if(isset($check_grupo_rifav->grupo_v) && in_array($item->EventId, explode(',', $check_grupo_rifav->grupo_v))) {
                $getTripsByAssetAndDate[$index]['rifav'][$key]->rifavo = 'v';
              }

              if(isset($check_grupo_rifav->grupo_o) && in_array($item->EventId, explode(',', $check_grupo_rifav->grupo_o))) {
                $getTripsByAssetAndDate[$index]['rifav'][$key]->rifavo = 'o';
              }
            }

          }

          return $getTripsByAssetAndDate;

        } else {

          $getTripsByAssetAndDate = \App\Models\Trips::on('sqlsrv_group')
          ->selectRaw("*, TripStart AT TIME ZONE 'UTC' AT TIME ZONE '$params->sql_fuso_name' AS TripStart, TripEnd AT TIME ZONE 'UTC' AT TIME ZONE '$params->sql_fuso_name' AS TripEnd")
          ->where('Trips.UnitID', $request->asset_id)
          ->where('Trips.TripStart', '<=', \Carbon\Carbon::parse($request->data_termino.' 23:59:59')->format('Y-m-d H:i:s'))
          ->where('Trips.TripStart', '>=', \Carbon\Carbon::parse($request->data_inicio.' 00:00:00')->format('Y-m-d H:i:s'))
          ->with('driver')
          ->with('assets')
          ->lock('WITH(NOLOCK)')
          ->orderBy(\DB::raw('CAST(Trips.TripStart as datetime2)', 'ASC'))
          ->get();

        foreach ($getTripsByAssetAndDate as $index => $value) {

          $getTripsByAssetAndDate[$index]['rifav'] = Events::join('LibraryEvents', 'LibraryEvents.EventId', '=', 'Events.EventId')
            ->selectRaw("*, StartDateTime AT TIME ZONE 'UTC' AT TIME ZONE '$params->sql_fuso_name' AS StartDateTime, EndDateTime AT TIME ZONE 'UTC' AT TIME ZONE '$params->sql_fuso_name' AS EndDateTime")
            ->where('Events.UnitId', $request->asset_id)
            // ->where('Events.DriverId', $request->driver_id)
            ->where('Events.EndDateTime', '<=', \Carbon\Carbon::parse($getTripsByAssetAndDate[$index]["TripEnd"])->timezone('UTC')->format('Y-m-d H:i:s'))
            ->where('Events.StartDateTime', '>=', \Carbon\Carbon::parse($getTripsByAssetAndDate[$index]["TripStart"])->timezone('UTC')->format('Y-m-d H:i:s'))
            ->whereNotIn('Events.EventId', [18, 17, 20, 19, 36, 37, 38, 39])
            ->get();

          foreach ($getTripsByAssetAndDate[$index]['rifav'] as $key => $item) {

            $getTripsByAssetAndDate[$index]['rifav'][$key]->rifavo = '';

            if(isset($check_grupo_rifav->grupo_r) && in_array($item->EventId, explode(',', $check_grupo_rifav->grupo_r))) {
              $getTripsByAssetAndDate[$index]['rifav'][$key]->rifavo = 'r';
            }

            if(isset($check_grupo_rifav->grupo_i) && in_array($item->EventId, explode(',', $check_grupo_rifav->grupo_i))) {
              $getTripsByAssetAndDate[$index]['rifav'][$key]->rifavo = 'i';
            }

            if(isset($check_grupo_rifav->grupo_f) && in_array($item->EventId, explode(',', $check_grupo_rifav->grupo_f))) {
              $getTripsByAssetAndDate[$index]['rifav'][$key]->rifavo = 'f';
            }

            if(isset($check_grupo_rifav->grupo_a) && in_array($item->EventId, explode(',', $check_grupo_rifav->grupo_a))) {
              $getTripsByAssetAndDate[$index]['rifav'][$key]->rifavo = 'a';
            }

            if(isset($check_grupo_rifav->grupo_v) && in_array($item->EventId, explode(',', $check_grupo_rifav->grupo_v))) {
              $getTripsByAssetAndDate[$index]['rifav'][$key]->rifavo = 'v';
            }

            if(isset($check_grupo_rifav->grupo_o) && in_array($item->EventId, explode(',', $check_grupo_rifav->grupo_o))) {
              $getTripsByAssetAndDate[$index]['rifav'][$key]->rifavo = 'o';
            }
          }

        }

        return $getTripsByAssetAndDate;

        }

      break;

      case "motorista":

      $getTripsByAssetAndDate = \App\Models\Trips::on('sqlsrv_group')
        ->selectRaw("*, TripStart AT TIME ZONE 'UTC' AT TIME ZONE '$params->sql_fuso_name' AS TripStart, TripEnd AT TIME ZONE 'UTC' AT TIME ZONE '$params->sql_fuso_name' AS TripEnd")
        ->where('Trips.UnitId', $request->asset_id)
        ->where('Trips.TripStart', '<=', \Carbon\Carbon::parse($request->data_termino.' 23:59:59')->format('Y-m-d H:i:s'))
        ->where('Trips.TripStart', '>=', \Carbon\Carbon::parse($request->data_inicio.' 00:00:00')->format('Y-m-d H:i:s'))
        ->with('driver')
        ->with('assets')
        ->lock('WITH(NOLOCK)')
        ->orderBy(\DB::raw('CAST(Trips.TripStart as datetime2)', 'ASC'))
        ->get();

      foreach ($getTripsByAssetAndDate as $index => $value) {

        $getTripsByAssetAndDate[$index]['rifav'] = Events::join('LibraryEvents', 'LibraryEvents.EventId', '=', 'Events.EventId')
          ->selectRaw("*, StartDateTime AT TIME ZONE 'UTC' AT TIME ZONE '$params->sql_fuso_name' AS StartDateTime, EndDateTime AT TIME ZONE 'UTC' AT TIME ZONE '$params->sql_fuso_name' AS EndDateTime")
          ->where('Events.UnitId', $request->asset_id)
          ->where('Events.DriverId', $request->user_id)
          ->where('Events.EndDateTime', '<=', \Carbon\Carbon::parse($getTripsByAssetAndDate[$index]["TripEnd"])->timezone('UTC')->format('Y-m-d H:i:s'))
          ->where('Events.StartDateTime', '>=', \Carbon\Carbon::parse($getTripsByAssetAndDate[$index]["TripStart"])->timezone('UTC')->format('Y-m-d H:i:s'))
          ->whereNotIn('Events.EventId', [18, 17, 20, 19, 36, 37, 38, 39])
          ->get();

          foreach ($getTripsByAssetAndDate[$index]['rifav'] as $key => $item) {

            $getTripsByAssetAndDate[$index]['rifav'][$key]->rifavo = '';

            if(isset($check_grupo_rifav->grupo_r) && in_array($item->EventId, explode(',', $check_grupo_rifav->grupo_r))) {
              $getTripsByAssetAndDate[$index]['rifav'][$key]->rifavo = 'r';
            }

            if(isset($check_grupo_rifav->grupo_i) && in_array($item->EventId, explode(',', $check_grupo_rifav->grupo_i))) {
              $getTripsByAssetAndDate[$index]['rifav'][$key]->rifavo = 'i';
            }

            if(isset($check_grupo_rifav->grupo_f) && in_array($item->EventId, explode(',', $check_grupo_rifav->grupo_f))) {
              $getTripsByAssetAndDate[$index]['rifav'][$key]->rifavo = 'f';
            }

            if(isset($check_grupo_rifav->grupo_a) && in_array($item->EventId, explode(',', $check_grupo_rifav->grupo_a))) {
              $getTripsByAssetAndDate[$index]['rifav'][$key]->rifavo = 'a';
            }

            if(isset($check_grupo_rifav->grupo_v) && in_array($item->EventId, explode(',', $check_grupo_rifav->grupo_v))) {
              $getTripsByAssetAndDate[$index]['rifav'][$key]->rifavo = 'v';
            }

            if(isset($check_grupo_rifav->grupo_o) && in_array($item->EventId, explode(',', $check_grupo_rifav->grupo_o))) {
              $getTripsByAssetAndDate[$index]['rifav'][$key]->rifavo = 'o';
            }
          }

      }

      return $getTripsByAssetAndDate;



      break;

      default:

        break;
      }

  }

}
