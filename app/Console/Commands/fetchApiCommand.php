<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Jobs\fetchApi;

class fetchApiCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'api:fetch';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'fetch the directory structure from the external API';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        fetchApi::dispatch();
        return Command::SUCCESS;
    }
}
