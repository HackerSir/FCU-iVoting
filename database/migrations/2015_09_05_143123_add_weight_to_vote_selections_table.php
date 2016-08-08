<?php

use Illuminate\Database\Migrations\Migration;

class AddWeightToVoteSelectionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('vote_selections', function ($table) {
            $table->float('weight')->default(1);
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
            $table->dropColumn('weight');
        });
    }
}
