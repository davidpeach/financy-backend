<?php

namespace App\Console\Commands;

use App\Jobs\GenerateForecast;
use Illuminate\Console\Command;

class FinancyForecastGenerateCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'financy:forecast:generate';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $this->call('migrate:fresh');
        $this->call('db:seed');

        GenerateForecast::dispatch(now()->addMonths(6));

        $this->call('financy:forecast:show');
    }
}
