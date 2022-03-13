<?php

namespace App\Console\Commands;

use App\Services\TckService;
use Illuminate\Console\Command;

class importProducts extends Command
{
    private TckService $tckService;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'lightspeed:import-products';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Import products';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct(TckService $tckService)
    {
        parent::__construct();

        $this->tckService = $tckService;
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        return 0;
    }
}
