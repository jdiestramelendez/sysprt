<?php

namespace App\Http\Middleware;

use Closure;

class SetDatabase
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {

        if (session()->exists('selected_group')) {
            \Config::set('database.connections.sqlsrv_group.host', session()->get('selected_group')->host);
            \Config::set('database.connections.sqlsrv_group.username', session()->get('selected_group')->username);
            \Config::set('database.connections.sqlsrv_group.password', session()->get('selected_group')->password);
            \Config::set('database.connections.sqlsrv_group.database', session()->get('selected_group')->database_name);

            return $next($request);

        } else {

            \Flash::error('Você deve selecionar um Grupo primeiro.');

            return redirect('/');
        }

        return $next($request);

    }
}
