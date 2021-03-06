<?php

use Illuminate\Database\Migrations\Migration;

class AddTitleColumnToVoteSelectionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('vote_selections', function ($table) {
            $table->text('title')->after('id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('vote_selections', function ($table) {
            $table->dropColumn('title');
        });
    }
}
