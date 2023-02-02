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
        Schema::create('notes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('agenda_id')->nullable()->references('id')->on('agendas');
            $table->enum('type', ['public', 'internal']);
            $table->string('name', 255);
            $table->date('date');
            $table->time('start_time', $precision=0)->nullable();
            $table->time('end_time', $precision=0)->nullable();
            $table->date('max_execute')->nullable();
            $table->text('issues')->nullable();
            $table->text('link_drive_notulen')->nullable();
            $table->enum('status', ['open', 'lock']);
            $table->foreignId('created_by')->nullable()->references('id')->on('users');
            $table->foreignId('updated_by')->nullable()->references('id')->on('users');
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
        Schema::dropIfExists('notes');
    }
};
