<?php

namespace App\Console\Commands;

use Carbon\Carbon;
use Illuminate\Console\Command;

class QuickBackup extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'db:quick-backup';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Quick backup';

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
        $now = Carbon::now();
        $filename = $now->format('Ymd_His') . '.sql';

        // database, destination, destinationPath, compression
        $this->call('db:backup', [
            '--database'        => 'mysql',
            '--destination'     => 'local',
            '--destinationPath' => $filename,
            '--compression'     => 'null',
        ]);
    }
}
