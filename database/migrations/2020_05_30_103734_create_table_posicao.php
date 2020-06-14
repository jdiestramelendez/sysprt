<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTablePosicao extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('posicao', function (Blueprint $table) {
            $table->Bigincrements('id');
            $table->string('idmodulo');
            $table->integer('idveiculo');
            $table->integer('idmotorista');
            $table->datetime('dataehora');
            $table->double('lat');
            $table->double('lng');
            $table->double('x');
            $table->double('y');
            $table->double('z');     
            $table->boolean('ignicao');
            $table->string('pacote');     
            $table->timestamps();

          //  $table->foreign('idveiculo')->references('id')->on('veiculos');
            $table->foreign('idmotorista')->references('id')->on('motoristas');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('posicao');
    }
}
