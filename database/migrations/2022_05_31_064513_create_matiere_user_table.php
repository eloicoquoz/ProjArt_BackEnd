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
        Schema::create('matiere_user', function (Blueprint $table) {
            $table->string('user_Email');
            $table->foreign('user_Email')
            ->references('Email')
            ->on('users')
            ->onDelete('restrict')
            ->onUpdate('restrict');
            $table->string('matiere_id');
            $table->foreign('matiere_id')
            ->references('id')
            ->on('matieres')
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
        Schema::dropIfExists('matiere_user');
    }
};
