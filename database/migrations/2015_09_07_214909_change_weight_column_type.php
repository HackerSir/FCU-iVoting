<?php

use Illuminate\Database\Migrations\Migration;

class ChangeWeightColumnType extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('vote_selections', function ($table) {
            $table->decimal('weight', 32, 16)->default(1)->change();
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
            $table->float('weight')->default(1)->change();
        });
    }
}
