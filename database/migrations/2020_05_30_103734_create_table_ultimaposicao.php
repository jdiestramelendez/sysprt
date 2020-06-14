<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTableUltimaPosicao extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('ultimaposicao', function (Blueprint $table) {
            $table->biginteger('id');
            $table->string('idmodulo');
            $table->integer('idveiculo');
            $table->integer('idmotorista');
            $table->datetime('dataehora');
            $table->double('lat');
            $table->double('lng');
            $table->double('x')->nullable();;
            $table->double('y')->nullable();;
            $table->double('z')->nullable();;     
            $table->boolean('ignicao');
            $table->string('pacote')->nullable();;     
            $table->timestamps();

            $table->foreign('idveiculo')->references('id')->on('veiculos');
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
        Schema::dropIfExists('ultimaposicao');
    }
}
