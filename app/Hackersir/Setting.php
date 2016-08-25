<?php

namespace Hackersir;

use Hackersir\Helper\MarkdownHelper;
use Illuminate\Database\Eloquent\Model;

/**
 * Hackersir\Setting
 *
 * @property string $id
 * @property string $type
 * @property string $desc
 * @property string $data
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @method static \Illuminate\Database\Query\Builder|\Hackersir\Setting whereId($value)
 * @method static \Illuminate\Database\Query\Builder|\Hackersir\Setting whereType($value)
 * @method static \Illuminate\Database\Query\Builder|\Hackersir\Setting whereDesc($value)
 * @method static \Illuminate\Database\Query\Builder|\Hackersir\Setting whereData($value)
 * @method static \Illuminate\Database\Query\Builder|\Hackersir\Setting whereCreatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\Hackersir\Setting whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class Setting extends Model
{
    protected $table = 'settings';
    protected $fillable = ['id', 'type', 'desc', 'data'];
    public $incrementing = false;
    //有效型態與對應簡介
    public static $types = [
        'text'      => '單行文字',
        'multiline' => '多行文字',
        'markdown'  => 'Markdown多行文字',
    ];

    public static function get($id)
    {
        $setting = self::find($id);
        if (!$setting) {
            return;
        }

        return $setting->getData();
    }

    public static function getRaw($id)
    {
        $setting = self::find($id);
        if (!$setting) {
            return;
        }

        return $setting->data;
    }

    public static function set($id, $data)
    {
        $setting = self::find($id);
        $setting->data = $data;
        $setting->save();
    }

    //取得型態
    public function getType()
    {
        //檢查是否為有效型態
        if (!in_array($this->type, array_keys(static::$types))) {
            //若不是，則自動選擇第一個型態
            return head(array_keys(static::$types));
        }

        return $this->type;
    }

    //取得型態簡介文字
    public function getTypeDesc()
    {
        return static::$types[$this->getType()];
    }

    //取得資料
    public function getData()
    {
        //依照型態進行不同處理
        if ($this->getType() == 'text') {
            return htmlspecialchars($this->data);
        }
        if ($this->getType() == 'multiline') {
            return nl2br(htmlspecialchars($this->data));
        }
        if ($this->getType() == 'markdown') {
            return MarkdownHelper::translate($this->data);
        }
    }
}
