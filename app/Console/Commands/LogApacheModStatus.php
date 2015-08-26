<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Log\Writer;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;

class LogApacheModStatus extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'apache-mod-status:log';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Log apache mod-status.';

    public static $logger;

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();

        if (is_null(self::$logger)) {
            self::initLogger();
        }
    }

    public static function getLogger() {
        if (is_null(self::$logger)) {
            self::initLogger();
        }
        return self::$logger;
    }

    private static function initLogger() {
        //TODO:: 保存天數設定
        //參考這個試試
        //http://laravel.com/api/5.0/Illuminate/Log/Writer.html#method_useDailyFiles
        //ENDTODO
        self::$logger = new Writer(new Logger('ModStatusLog'));
        self::$logger->useDailyFiles(storage_path('logs-mod-status/mod-status.log'));
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
