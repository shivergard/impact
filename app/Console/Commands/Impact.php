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

        }else{
            $this->error('Connection Unsuccess');
        }

    }
}
