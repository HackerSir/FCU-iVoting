<?php

use Hackersir\Setting;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddContactUrlSetting extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Setting::create([
            'id'   => 'contact-url',
            'desc' => '聯繫用網址',
            'type' => 'url',
        ]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Setting::find('contact-url')->delete();
    }
}
