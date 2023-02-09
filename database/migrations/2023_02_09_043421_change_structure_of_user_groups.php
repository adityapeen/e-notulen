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
        Schema::table('user_groups', function (Blueprint $table) {
            $table->dropForeign(['group_id']);
        });
        Schema::table('user_groups', function (Blueprint $table) {
            $table->renameColumn('group_id', 'agenda_id');
        });
        Schema::table('user_groups', function (Blueprint $table) {
            $table->foreign('agenda_id')
                    ->references('id')->on('agendas')
                    ->onDelete('cascade')
                    ->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('user_groups', function (Blueprint $table) {
            $table->dropForeign(['agenda_id']);
            $table->renameColumn('agenda_id', 'group_id');
        });
    }
};
