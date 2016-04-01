<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

use App\Helpers\RabbitMq;
use App\Helpers\Interest;

use Config;

class Impact extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'impact';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Run Impact Service';


    private $rabbit = false;

    private $interest = false;

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $this->comment("init the impact");

        $this->rabbit = RabbitMq::makeConnection(Config::get('impact'));

        $this->interest = new Interest();

        if ($this->rabbit->getStatus()){

            $this->rabbit->getNews(
                array($this, 'prepareMessageResponse')
            );

            while (count($this->rabbit->getChannel()->callbacks)) {
                $this->rabbit->getChannel()->wait();
            }

        }else{
            $this->error('Connection Unsuccess');
        }

    }

    public function prepareMessageResponse($message = false){

        $return = false;

        if ($message){
                
            $return = json_decode($message->body, true);

            if (json_last_error() == JSON_ERROR_NONE && is_array($return)){
                
                $return['status'] = 1;

                $responsed = false;

                if (
                    $this->rabbit->canOperate($return) && 
                    ($responsed = $this->interest->exec($return)) &&
                    $responsed &&
                    $this->rabbit->response($responsed)
                ){

                    $message->delivery_info['channel']->basic_ack($message->delivery_info['delivery_tag']); 
                }

            }else{
                $this->error($message->body);
                $return = array('status' => 1);
            }


            $this->info(json_encode($return));  
        
        }



        return json_encode($return);
    }
}
