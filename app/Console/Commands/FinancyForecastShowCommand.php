<?php

namespace App\Console\Commands;

use App\Http\Resources\TransactionResource;
use App\Models\Transaction;
use Illuminate\Console\Command;

class FinancyForecastShowCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'financy:forecast:show';

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
        $transactions = TransactionResource::collection(
            Transaction::where('date', '>', now())
                ->orderBy('date', 'asc')
                ->get()
        );

        $this->table(
            ['Amount', 'Name', 'Date', 'Closing Balance', 'Type'],
            $transactions->toArray(request())
        );
    }
}
