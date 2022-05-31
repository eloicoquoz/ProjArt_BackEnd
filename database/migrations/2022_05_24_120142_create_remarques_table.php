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
        Schema::create('remarques', function (Blueprint $table) {
            $table->id();
            $table->string('Titre');
            $table->string('Description');
            $table->string('Visibilite');
            $table->date('Date');
            $table->string('user_Email');
            $table->foreign('user_Email')
            ->references('Email')
            ->on('users')
            ->onDelete('restrict')
            ->onUpdate('restrict');
            $table->integer('cours_id')->unsigned();
            $table->foreign('cours_id')
            ->references('id')
            ->on('cours')
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
        Schema::dropIfExists('remarques');
    }
};
