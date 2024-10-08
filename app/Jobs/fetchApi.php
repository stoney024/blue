<?php

namespace App\Jobs;

use App\Models\Sync;
use Illuminate\Bus\Queueable;
use App\Repositories\ApiRepository;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Contracts\Queue\ShouldBeUnique;

class fetchApi implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $apiRepository = new ApiRepository();

        $result = $apiRepository->fetch();

        $sync = Sync::create();
        processItems::dispatch($result, $sync->id);
    }
}
