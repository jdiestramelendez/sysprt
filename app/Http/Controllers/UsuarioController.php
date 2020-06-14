<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\User;
use Illuminate\Support\Facades\Hash;

class UsuarioController extends Controller
{
    public function index(){

        $usuarios = DB::connection('conSistema')->table('users as u')
        ->select('u.id',  'u.name',  'u.email','u.created_at','e.nome' )
                                     ->leftJoin('empresa as e', 'u.idEmpresa', '=', 'e.id')
                                     ->where('u.idEmpresa',\Auth::user()->idEmpresa)
                                     ->get();


        return view('usuario.index',compact('usuarios'));
    }

    public function create(){
        return view('usuario.create');
    }

    
    public function store(Request $request){     
        $usuario =  User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'idEmpresa' => \Auth::user()->idEmpresa,
        ]);
        
        $usuario->save();
 
        return redirect('/usuarios');
     }
 
     public function edit($id){
         $usuario = User::where('id',$id)->first();
         return view('usuarios.edit',compact('usuario'));
     }
 
     public function update(Request $request){
          
        
 
       return redirect('/usuarios');
     }
 
     public function delete($id){
 
          $usuario = User::where('id',$id)->first();
          $usuario->delete();
 
          return redirect('/usuarios');
     }
}
