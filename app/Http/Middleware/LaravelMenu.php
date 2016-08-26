<?php

namespace App\Http\Middleware;

use Closure;
use Menu;

class LaravelMenu
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \Closure $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        //左側
        Menu::make('left', function ($menu) {
            /* @var \Lavary\Menu\Builder $menu */
            $menu->add('首頁', ['route' => 'home']);
        });
        //右側
        Menu::make('right', function ($menu) {
            /* @var \Lavary\Menu\Builder $menu */
            $menu->add('票選活動', ['route' => 'voteEvent.index'])->active('voteEvent/*');
            //會員
            if (auth()->check()) {
                //管理員
                if (auth()->check() && auth()->user()->isAdmin()) {
                    /** @var \Lavary\Menu\Builder $adminMenu */
                    $adminMenu = $menu->add('管理選單', 'javascript:void(0)');

                    $adminMenu->add('成員清單', ['route' => 'member.list'])->active('member/*');
                    $adminMenu->add('主辦單位清單', ['route' => 'organizer.index'])->active('organizer/*');
                    $adminMenu->add('網站設定', ['route' => 'setting.index'])->active('setting/*');
                    $adminMenu->add('Queue狀態', ['route' => 'queue-status'])->active('queue-status/*');
                    $adminMenu->add('統計', ['route' => 'stats.index'])->active('stats/*');
                    $adminMenu->add(
                        '記錄檢視器 <i class="glyphicon glyphicon-new-window"></i>',
                        ['route' => 'log-viewer::dashboard']
                    )->link->attr('target', '_blank');
                }
                /** @var \Lavary\Menu\Builder $userMenu */
                $userMenu = $menu->add(auth()->user()->getNickname(), 'javascript:void(0)');
                $userMenu->add('個人資料', ['route' => 'member.profile'])->active('profile/*');
                $userMenu->add('修改密碼', ['route' => 'member.change-password']);
                $userMenu->add('登出', ['route' => 'member.logout']);
            } else {
                //遊客
                $menu->add('登入', ['route' => 'member.login']);
            }
        });

        return $next($request);
    }
}
