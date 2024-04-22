<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Retrieve existing agenda_id values from notes table
        $existingNotes = DB::table('notes')->select('id', 'agenda_id')->get();

        // Insert data into note_agenda pivot table
        foreach ($existingNotes as $note) {
            DB::table('note_agenda')->insert([
                'note_id' => $note->id,
                'agenda_id' => $note->agenda_id,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // Remove data from note_agenda pivot table if necessary
        DB::table('note_agenda')->truncate();
    }
};
