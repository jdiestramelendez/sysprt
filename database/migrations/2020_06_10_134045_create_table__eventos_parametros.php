<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTableEventosParametros extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('EventosParametros', function (Blueprint $table) {
            $table->integer('idEvento');
            $table->integer('idParametro');
            $table->double('valor');
            $table->string('descricaoParametro');
            $table->timestamps();

            $table->foreign('idEvento')->references('id')->on('eventos');
            $table->foreign('idParametro')->references('id')->on('Parametros');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('EventosParametros');
    }
}
