<?php

use Illuminate\Database\Migrations\Migration;

class AddShowResultColumnToEventsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('vote_events', function ($table) {
            $table->enum('show_result', [
                'always',
                'after-vote',
                'after-event',
            ])->default('after-event');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('vote_events', function ($table) {
            $table->dropColumn('show_result');
        });
    }
}
