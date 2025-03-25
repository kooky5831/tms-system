<?php

namespace App\Facades;

use Illuminate\Support\Facades\Facade;
use Illuminate\Support\Facades\Log;
use Monolog\Handler\StreamHandler;
use Monolog\Formatter\LineFormatter;
use Monolog\Logger;
use Illuminate\Support\Carbon;

class SchedulerLog extends Facade
{
    /**
     * Get Facade Accessor for Current Class
     *
     * @return string Accessor
     */
    protected static function getFacadeAccessor()
    {
        return 'schedulerlog';
    }
    
    /**
     * Handle dynamic, static calls to the object.
     *
     * @param  string  $method
     * @param  array   $args
     * @return mixed
     */
    public static function __callStatic($method, $args)
    {
        if (! isset($args[0])) {
            return;
        }

        $message = $args[0];
        //$file = isset($args[1]) ? $args[1] : 'laravel';
        $file = isset($args[1]) ? $args[1] : 'scheduler';

        // Maximum number of lines in one log file
        $max = 10000;

        // Get Log Instance
        $logger = Log::getLogger();

        // Trim log file to a max length
        $path = storage_path('/logs/schedulerlogs/'.$file.'-'. Carbon::now()->toDateString().'.log');
        if (! file_exists($path)) {
            fopen($path, "w");
        }
        $lines = file($path);
        if (count($lines) >= $max) {
            file_put_contents($path, implode('', array_slice($lines, -$max, $max)));
        }

        // Define custom Monolog handler
        $handler = new StreamHandler($path, Logger::DEBUG);
        $handler->setFormatter(new LineFormatter(null, null, true, true));

        // Set defined handler and log the message
        $logger->setHandlers([$handler]);
        $logger->$method($message);
    }
}

