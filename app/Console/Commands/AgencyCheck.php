<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

use App\Helpers\RabbitMq;
use App\Helpers\Agency;

use Config;

class AgencyCheck extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'impact:agency';

    private $rabbit;

    private $agency;

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Register test for agency and informs about compleate';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $this->comment("init the agency");

        if (Config::get('agency_impact.active')){
            $this->rabbit = RabbitMq::makeConnection(Config::get('agency_impact'));

            $this->agency = new Agency();

            if ($this->rabbit->getStatus()){

                $impacting = true;

                $this->rabbit->getNewsSolved(
                    array($this, 'prepareMessageResponse')
                );

                $this->publishNews(true);

                while (count($this->rabbit->getChannel()->callbacks) > 0) {
                    //process
                    $this->rabbit->getChannel()->wait();
                }  

            }else{
                $this->error('Connection Unsuccess');
            }
        }
    }


    public function prepareMessageResponse($message = false){

        $return = false;

        if ($message){
            
            $return = json_decode($message->body, true);

            $responsed = false;

            if (json_last_error() == JSON_ERROR_NONE && is_array($return)){

                $processed = false;

                if ($return['action'] == 'create_test'){
                    $testResponse = $this->agency->prepareTest($return);
                    if ($testResponse)
                        $processed = $this->rabbit->response($testResponse);    
                }
                

                if ($processed){
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
