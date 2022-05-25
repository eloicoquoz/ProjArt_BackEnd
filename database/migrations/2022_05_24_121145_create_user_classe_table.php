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
        Schema::create('user_classe', function (Blueprint $table) {
            $table->string('classe_id');
            $table->string('user_Email');
            $table->foreign('user_Email')
            ->references('Email')
            ->on('users')
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
        Schema::dropIfExists('user_classe');
    }
};
