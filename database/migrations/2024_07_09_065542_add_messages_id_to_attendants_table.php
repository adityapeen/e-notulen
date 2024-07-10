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
        Schema::table('attendants', function (Blueprint $table) {
            $table->string('message_id',64)->nullable()->after('mom_sent');
        });

        Schema::table('mom_recipients', function (Blueprint $table) {
            $table->string('message_id',64)->nullable()->after('mom_sent');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('attendants', function (Blueprint $table) {
            $table->dropColumn('message_id');
        });
        Schema::table('mom_recipients', function (Blueprint $table) {
            $table->dropColumn('message_id');
        });
    }
};
