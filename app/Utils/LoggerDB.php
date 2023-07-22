<?php

namespace App\Utils;


use Monolog\Logger;
use Monolog\Handler\StreamHandler;

class AlinLogger
{
    public $logDB;
    public $timeStartDB;
    public $timeEndDB;

    public $memoryStartDB;
    public $memoryEndDB;

    public function runLogDB()
    {
        $this->timeStartDB = microtime(true);
        $this->memoryStartDB = memory_get_usage();
    }

    public function stopLogDB()
    {
        $this->timeEndDB = microtime(true);
        $this->memoryEndDB = memory_get_usage();
    }

    public function writeLogDB($name, $path, array $context, $level = Logger::DEBUG)
    {
        $this->logDB = new Logger($name);
        $duration = $this->timeEndDB - $this->timeStartDB;
        $memory = $this->memoryEndDB - $this->memoryStartDB;
        $cpu_usage = getrusage();
        $context['time'] = $duration;
        $context['memory'] = $memory;
        $context['cpu'] = $cpu_usage['ru_utime.tv_usec'];
        $this->logDB->pushHandler(new StreamHandler($path, $level));

        $message = 'call db';
        $this->logDB->log($level, $message, $context);
    }
}
