<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Motoristas;
use Illuminate\Support\Facades\DB;

class MotoristasController extends Controller
{
    public function index(){

        $motoristas = Motoristas::all();
        return view('motorista.index',compact('motoristas'));
    }

    public function create(){
        return view('motorista.create');
    }

    public function store(Request $request){      
       $motorista = Motoristas::create($request->all());
       $motorista->save();

       return redirect('/motoristas');
    }

    public function edit($matricula){
        $motorista = Motoristas::where('matricula',$matricula)->first();
        return view('motorista.edit',compact('motorista'));
    }

    public function update(Request $request){

      $motorista = Motoristas::where('id',$request->id)->first();

      $motorista->matricula =  $request->matricula ;
      $motorista->nomedeescala =  $request->nomedeescala ;
      $motorista->nomecompleto =  $request->nomecompleto ;
      $motorista->datanascimento =  $request->datanascimento ;
      $motorista->cpf =  $request->cpf ;
      $motorista->idcracha =  $request->idcracha ;

      $motorista->save();

      return redirect('/motoristas');
    }

    public function delete($matricula){

         $motorista = Motoristas::where('matricula',$matricula)->first();
         $motorista->delete();

         return redirect('/motoristas');
    }

}
