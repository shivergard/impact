<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

use App\Helpers\RabbitMq;
use App\Helpers\Interest;

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
    }
}
