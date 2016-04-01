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

            $rabbit->postNews(
                array(
                    'sum' => rand(1 , 5000) , 
                    'days' => rand(1 , 150)
                )
            );

            $rabbit->exitCon();

        }else{
            $this->error('Connection Unsuccess');
        }

    }
}
