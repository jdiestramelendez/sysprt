<?php

Route::group(
  ['middleware' => ['auth']],
  function () {
    Route::resource('users', 'UserController');
    Route::resource('roles', 'RoleController');
  });

Route::get('/', 'HomeController@index');

Auth::routes();

//Rotas para Motorista
Route::get('/motoristas','MotoristasController@index');
Route::get('/motoristas/novo','MotoristasController@create');
Route::post('/motoristas/store','MotoristasController@store');
Route::get('/motoristas/edit/{matricula}','MotoristasController@edit');
Route::post('/motoristas/update','MotoristasController@update');
Route::get('/motoristas/delete/{matricula}','MotoristasController@delete');


//Rotas para usuário
Route::get('/usuarios',"UsuarioController@index");
Route::get('/usuarios/novo','UsuarioController@create');
Route::post('/usuarios/store','UsuarioController@store');
Route::get('/usuarios/edit/{id}','UsuarioController@edit');
Route::post('/usuarios/update','UsuarioController@update');
Route::get('/usuarios/delete/{id}','UsuarioController@delete');

//Rotas para eventos
Route::get('/eventos','EventosController@index');
Route::post('/geteventos','EventosController@geteventos');
Route::get('/eventos/parametros','EventosController@getparametros');