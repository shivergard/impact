<?php

namespace App\Helpers;

use PhpAmqpLib\Connection\AMQPStreamConnection;

class RabbitMq {

    private $status = false;

    private $connection = false;

    private $channel = false;

    public static function makeConnection($config){
        $newRabbit = new RabbitMq();

        try {
            $newRabbit->setConnection(
                 new AMQPStreamConnection(
                    $config['host'], 
                    5672, 
                    $config['user'], 
                    $config['password'],
                    $config['user']
                )
            );


            $newRabbit->setChannel($newRabbit->getConnection()->channel());  


        } catch (ErrorException $e) {
            dd($e);
        }

        

        return $newRabbit;  
    }

    public function getStatus(){
        return $this->status;
    }

    public function setConnection($con){
        $this->connection = $con;
        $this->status = true;
    }

    public function getConnection(){
        return $this->connection;
    }

    public function setChannel($channel){
        $this->channel = $channel;
    }

}