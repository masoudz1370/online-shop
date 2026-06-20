<?php

namespace App\Jobs;

use App\Models\Log;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Foundation\Queue\Queueable as FoundationQueueable;

class LogToMongo implements ShouldQueue
{
    use Queueable, FoundationQueueable;

    public array $payload;

    /**
     * Create a new job instance.
     */
    public function __construct(array $payload)
    {
        $this->payload = $payload;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        Log::create([
            'section' => $this->payload['section'] ?? null,
            'action' => $this->payload['action'] ?? null,
            'user_id' => $this->payload['user_id'] ?? null,
            'data' => $this->payload['data'] ?? [],
        ]);
    }
}
