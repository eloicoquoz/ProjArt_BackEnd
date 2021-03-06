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
        Schema::create('classe_cours', function (Blueprint $table) {
            $table->integer('cours_id')->unsigned();
            $table->string('classe_id');
            $table->foreign('cours_id')->references('id')->on('cours')
            ->onDelete('restrict')
            ->onUpdate('restrict');
            $table->foreign('classe_id')->references('id')->on('classes')
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
        Schema::dropIfExists('classe_cours');
    }
};
