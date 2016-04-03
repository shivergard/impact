<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Helpers\RabbitMq;
use App\Helpers\Interest;

use App\Applicants;

use Config;
use Cache;

use Carbon\Carbon;

class ImpactProvider extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'impact:provide';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Provides impact';

    private $rabbit;


    private $interest = false;

    private $lastAct = false;

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $this->comment("init the impact provider");

        $this->rabbit = RabbitMq::makeConnection(Config::get('impact'));

        if ($this->rabbit->getStatus()){

            $this->interest = new Interest();

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

    public function publishNews($valid = false){
        
            if ($valid)
                $roulete = 1;
            else    
                $roulete = rand(1 , 10);

            
            if ($roulete > 6){
                if ($roulete == 7){
                   $data = array(
                        'sum' => rand(-100 , 0) , 
                        'days' => rand(1 , 150)
                    ) ;
                }else if ($roulete == 8){
                   $data = array(
                        'sum' => rand(0 , 100000000000) , 
                        'days' => rand(0 , 100000000000)
                    ) ;
                }else if ($roulete == 9){
                   $data = array(
                        'sum' => NULL , 
                        'days' => rand(0 , 100000000000)
                    ) ;
                }else{
                    $data = array();    
                }
            }else{
                $data = array(
                    'sum' => rand(1 , 5000) , 
                    'days' => rand(1 , 150)
                );
            } 

            $this->info(json_encode($data));

            $this->rabbit->postNews(
                $data
            );
    }


    public function prepareMessageResponse($message = false){

        $return = false;

        if ($message){  

            $lastAct = false;

            if (!$lastAct || (time()-$lastAct > 30)){
                $lastAct = time();
                $this->publishNews(true);
                $this->publishNews();
            }


            
            $return = json_decode($message->body, true);

            $responsed = false;

            if (json_last_error() == JSON_ERROR_NONE && is_array($return)){

                // validate token - select all active apllicants
                $applicant = Applicants::where('creditals' , str_replace("agent_", "", $return['token']));

                if ($applicant->count() > 0){

                    $applicantModel = $applicant->first();

                    if ($applicantModel->status == 1 || Carbon::now()->diffInHours($applicantModel->created_at) <= 48){
                        if ($this->interest->exec($return)){
                            $return['status'] = 1;
                        }else{
                            $return['status'] = 2;
                        }
                    }else{
                        $return['status'] = 0;
                    }

                }

            }else{
                $return = array('status' => 0);
            }

            $message->delivery_info['channel']->basic_ack($message->delivery_info['delivery_tag']); 

            $expiresAt = Carbon::now()->addMinutes(1);
            Cache::put('last_updated', json_encode($return), $expiresAt);

        }

    }
}