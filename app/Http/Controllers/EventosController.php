<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Eventos;
use App\Models\Parametros;

class EventosController extends Controller
{
    public function index(){
        return view('eventos.index');
    }

    public function geteventos(){
      return  Eventos::all();
    }

    public function getParametros(){
        return Parametros::all();
    }
}
