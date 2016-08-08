<?php

use App\Setting;
use Illuminate\Database\Migrations\Migration;

class AddRedirectSetting extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Setting::create([
            'id'   => 'auto-redirect',
            'desc' => '訪問首頁時，自動跳轉至此網址（請填寫單一網址，留白為不啟用）',
        ]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Setting::find('auto-redirect')->delete();
    }
}
