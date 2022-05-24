<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('salle_cours', function (Blueprint $table) {
            $table->integer('salle_id')->unsigned();
            $table->integer('cours_id')->unsigned();
            $table->dateTime('Debut');
            $table->dateTime('Fin');
            $table->foreign('salle_id')->references('id')->on('salles')
            ->onDelete('restrict')
            ->onUpdate('restrict');
            $table->foreign('cours_id')->references('id')->on('cours')
            ->onDelete('restrict')
            ->onUpdate('restrict');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('salle_cours');
    }
};
