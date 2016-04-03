<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

use App\Helpers\RabbitMq;
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


    public function prepareMessageResponse($message){

    }
}
