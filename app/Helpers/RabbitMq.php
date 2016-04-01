<?php

namespace App\Helpers;

use PhpAmqpLib\Connection\AMQPStreamConnection;

class RabbitMq {

    private $status = false;

    public static function makeConnection($config){
        $newRabbit = new RabbitMq();



        return $newRabbit;  
    }

    public function getStatus(){
        return $this->status;
    }

}