<?php

use Illuminate\Support\Facades\Auth;

return array(

    /**
     * 巡覽列
     *
     * 基本格式：'連結名稱' => '連結路由'
     *
     * 多層：二級選單以下拉式選單呈現，更多層級以巢狀顯示，太多層可能會超過螢幕顯示範圍
     * 外部連結：在連結路由部分，直接填上完整網址（開頭需包含協定類型）
     * 新分頁開啟：外部連結或路由開頭為「!」者會由新分頁開啟
     */

    //基本巡覽列
    'navbar' => array(
        '票選活動' => 'vote-event',
    ),

    //會員
    'member' => array(
        '%user%' => array(
            '個人資料' => 'member/profile',
            '修改密碼' => 'member/change-password',
            '登出' => 'member/logout'
        )
    ),

    //工作人員
    'staff' => array(
        '工作人員' => array()
    ),

    //管理員
    'admin' => array(
        '管理員' => array(
            '成員清單' => 'member',
            '主辦單位清單' => 'organizer',
            '網站設定' => 'setting',
            '記錄檢視器' => '!log'
        )
    ),

    //遊客
    'guest' => array(
        '登入' => 'member/login'
    ),
);
