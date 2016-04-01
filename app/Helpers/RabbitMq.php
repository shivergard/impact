<?php

namespace App\Helpers;

use PhpAmqpLib\Connection\AMQPStreamConnection;

class RabbitMq {

    public static function makeConnection($config){
        $newRabbit = new RabbitMq();

        return $newRabbit;
    }

}