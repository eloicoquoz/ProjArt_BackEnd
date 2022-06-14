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
        Schema::create('destinataires', function (Blueprint $table) {
            $table->integer('notification_id')->unsigned();
            $table->string('user_Email');
            $table->boolean('Lu');
            $table->foreign('user_Email')
            ->references('Email')
            ->on('users')
            ->onDelete('restrict')
            ->onUpdate('restrict');
            $table->foreign('notification_id')->references('id')->on('notifications')
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
        Schema::dropIfExists('destinataires');
    }
};
