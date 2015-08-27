<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Log\Writer;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;

class LogApacheStatus extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'apache-status:log';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Log apache status.';

    public static $logger;

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    public static function initLogger() {
        if (is_null(self::$logger)) {
            //TODO:: 保存天數設定
            //參考這個試試
            //http://laravel.com/api/5.0/Illuminate/Log/Writer.html#method_useDailyFiles
            //ENDTODO
            self::$logger = new Writer(new Logger('StatusLog'));
            self::$logger->useDailyFiles(storage_path('logs-status/mod-status.log'));
        }
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        //self::$logger->info('Test');
    }

    protected function getServerStatus() {

    }
}

LogApacheStatus::initLogger();
