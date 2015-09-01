<?php

namespace App\Console\Commands;

use App\Helper\LogHelper;
use App\User;
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
            ->where('register_at', '<', Carbon::now()->subWeek())
            ->lists('email', 'id')  //$column, $key
            ->toArray();

        if(!empty($unConfirmUsers)) {
            User::destroy(array_keys($unConfirmUsers));

            LogHelper::info('[CommandExecuted] 刪除超過一週未驗證的帳號', array_values($unConfirmUsers));
        }
    }
}
