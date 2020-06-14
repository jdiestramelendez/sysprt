<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\DatatablesOrder;
use App\Models\EventsOrder;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        
        return view('map');
    }

    public function selectgroup(Request $request)
    {
        $group = \DB::table('groups')
            ->where('id', $request->group_id)
            ->first();

        session()->put('selected_group_id', $request->group_id);

        session()->put(['selected_group' => $group]);

        return response()->json(session()->get('selected_group'), 201);
        
    }

    public function datatablesOrder(Request $request)
    {
        $model = $request->model;
        $titles = $request->titles;
        $names = $request->names;

        $datatables_order = DatatablesOrder::where('model', $request->model)
                            ->where('user', \Auth::user()->id)
                            ->first();

        if($datatables_order) {

            $datatables_order->titles   = $request->titles;
            $datatables_order->names    = $request->names;
            $datatables_order->model    = $request->model;
            $datatables_order->user     = \Auth::user()->id;
            $datatables_order->save();

        } else {

            $datatables_order           = new DatatablesOrder;
            $datatables_order->titles   = $request->titles;
            $datatables_order->names    = $request->names;
            $datatables_order->model    = $request->model;
            $datatables_order->user     = \Auth::user()->id;
            $datatables_order->save();

        }

        return response()->json('Datatables reordenado com sucesso.', 201);

    }

    public function datatablesOrderReset($model)
    {
        $datatables_order = DatatablesOrder::where('model', $model)
                            ->where('user', \Auth::user()->id)
                            ->first();
                            
        if($datatables_order) {

            $datatables_order->delete($datatables_order->id);

            return response()->json('Datatables restaurado com sucesso.', 201);

        } else {

            return response()->json('Datatables não alterado.', 201);

        }

    }

    public function eventsOrder(Request $request)
    {
        $event_id = $request->event_id;
        $status = $request->status;

        $events_order = EventsOrder::where('user', \Auth::user()->id)
            ->first();

        if ($events_order) {

            $events_order->event_id = $request->event_id;
            $events_order->status = $request->status;
            $events_order->user = \Auth::user()->id;
            $events_order->save();

        } else {

            $events_order = new EventsOrder;
            $events_order->event_id = $request->event_id;
            $events_order->status = $request->status;
            $events_order->user = \Auth::user()->id;
            $events_order->save();

        }

        return response()->json('Eventos reordenados com sucesso.', 201);

    }

    public function getSystemConfigs()
    {

        $configs = \DB::table('configs')->first();

        return response()->json([
            'versao_sistema' => $configs->versao_sistema,
        ], 201);
    }

    public function GetSelectedGroup()
    {
        $group = \DB::table('groups')
            ->where('id', session()->get('selected_group_id'))
            ->first();

        return response()->json([
            'selected_group' => $group
        ], 201);
    }
    

}