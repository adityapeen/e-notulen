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
        Schema::create('mom_recipients', function (Blueprint $table) {
            $table->id();
            $table->foreignId('note_id')->references('id')->on('notes');
            $table->foreignId('user_id')->references('id')->on('users');
            $table->dateTime('mom_sent')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('mom_recipients');
    }
};
