<?php

namespace App\Helpers;

use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;
use Config;

class RabbitMq {

    private $status = false;

    private $connection = false;

    private $channel = false;

    private $queue = false;

    private $queue_declared = false;

    public static function makeConnection($config){
        $newRabbit = new RabbitMq();

        try {
            $newRabbit->setConnection(
                 new AMQPStreamConnection(
                    $config['host'], 
                    5672, 
                    $config['user'], 
                    $config['password']
                )
            );


            $newRabbit->setChannel($newRabbit->getConnection()->channel());  

            $newRabbit->setQueue($config['queue']);


        } catch (PhpAmqpLib\Exception\AMQPRuntimeException $e) {
            //no error just returns failed rabbit
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

    public function setQueue($queue){
        $this->queue = $queue;
    }

    public function getConnection(){
        return $this->connection;
    }

    public function setChannel($channel){
        $this->channel = $channel;
    }

    public function getChannel(){
        return $this->channel;
    }



    private function declareQueue(){

        $this->channel->queue_declare($this->queue, false, true, false, false);

        $this->channel->exchange_declare($this->queue, 'direct', false, true, false);
        $this->channel->queue_bind($this->queue, $this->queue);

        register_shutdown_function(
            array($this, 'exitCon'), 
            $this->channel, $this->connection
        );

        $this->queue_declared = true;
            
    }

    public function getNews($callBack){

        //if (!$this->queue_declared){
        //    $this->declareQueue();
        //}

        $this->channel->basic_consume($this->queue, Config::get('app.name') , false, false, false, false, $callBack);

    }

    public function canOperate($message){
        $return = false;
        
        if (!isset($message['token']) || $message['token'] != Config::get('app.name')){
            $return = true;
        }
            

        return $return;
    }


    public function response(Array $response){

        $return = false;

        if (is_array($response)){
            $return = true; 
            $response['token'] = Config::get('app.name');
            $message = new AMQPMessage(json_encode($response), array('content_type' => 'application/json', 'delivery_mode' => AMQPMessage::DELIVERY_MODE_PERSISTENT));
            $this->channel->basic_publish($message, '', 'solved-'.$this->queue);
        }

        return $return;
    }

    public function postNews($news){

        if (!$this->queue_declared){
            $this->declareQueue();
            $this->channel->confirm_select();
        }

        $message = new AMQPMessage(json_encode($news), array('content_type' => 'application/json', 'delivery_mode' => AMQPMessage::DELIVERY_MODE_PERSISTENT));
        $this->channel->basic_publish($message, '' ,$this->queue);

    }

    public function exitCon(){
        $this->channel->close();
        $this->connection->close();
    }

}