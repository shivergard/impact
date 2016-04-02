<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Helpers\RabbitMq;

use Config;

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
    protected $description = 'Provides impacts';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $this->comment("init the impact");

        $rabbit = RabbitMq::makeConnection(Config::get('impact'));

        if ($rabbit->getStatus()){

            $impacting = true;

            while($impacting){
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

                $rabbit->postNews(
                    $data
                );

                sleep(2);               
            }


            $rabbit->exitCon();

        }else{
            $this->error('Connection Unsuccess');
        }

    }
}
