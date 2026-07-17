<?php

namespace support\bootstrap;

use Illuminate\Database\Capsule\Manager as Capsule;
use Workerman\Worker;

class Database
{
    public static function start(?Worker $worker): void
    {
        $capsule = new Capsule;
        $capsule->addConnection(config('database.connections.mysql'));
        $capsule->setAsGlobal();
        $capsule->bootEloquent();
    }
}
