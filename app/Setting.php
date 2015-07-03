<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    protected $table = 'settings';
    protected $fillable = ['id', 'desc', 'data'];

    static public function get($id)
    {
        $setting = self::find($id);
        if (!$setting) {
            return null;
        }
        return $setting->data;
    }

    static public function set($id, $data)
    {
        $setting = self::find($id);
        $setting->data = $data;
        $setting->save();
    }
}
