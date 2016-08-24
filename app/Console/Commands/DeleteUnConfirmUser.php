<?php

namespace App\Console\Commands;

use Hackersir\Helper\LogHelper;
use Hackersir\User;
use Carbon\Carbon;
use Illuminate\Console\Command;

class DeleteUnConfirmUser extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'user:delete-unconfirm';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Delete user who unconfirm in week.';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $unConfirmUsers = User::whereNull('confirm_at')
            ->where('register_at', '<', Carbon::now()->subMonth())
            ->lists('email', 'id')//$column, $key
            ->toArray();

        if (!empty($unConfirmUsers)) {
            User::destroy(array_keys($unConfirmUsers));

            $userArray = array_values($unConfirmUsers);
            LogHelper::info('[CommandExecuted] 刪除超過一個月未驗證的帳號' . '(' . count($userArray) . '個)', $userArray);
        } else {
            LogHelper::info('[CommandExecuted] 嘗試刪除超過一個月未驗證的帳號，無符合條件之帳號');
        }
    }
}
