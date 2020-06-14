<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTableRegistroeventos extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('registroeventos', function (Blueprint $table) {
            $table->bigincrements('id');
            $table->integer('idEvento');
            $table->string('idmodulo');
            $table->integer('idveiculo');
            $table->integer('idmotorista');
            $table->datetime('horainicio');
            $table->datetime('horafim');
            $table->double('valor');
            $table->biginteger('posicaoinicio');
            $table->biginteger('posicaofim');
            $table->timestamps();

            $table->foreign('idEvento')->references('id')->on('eventos');
            $table->foreign('idmotorista')->references('id')->on('motoristas');
            $table->foreign('idveiculo')->references('id')->on('veiculos');
            $table->foreign('posicaoinicio')->references('id')->on('posicao');
            $table->foreign('posicaofim')->references('id')->on('posicao');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('registroeventos');
    }
}
