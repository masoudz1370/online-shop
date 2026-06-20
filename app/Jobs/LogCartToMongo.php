<?php

namespace App\Jobs;

use App\Models\CartLog;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class LogCartToMongo implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    // تغییر از protected به public برای دسترسی راحت‌تر (اختیاری ولی بهتر)
    public $logData;

    public function __construct(array $logData)
    {
        $this->logData = $logData;
    }

    public function handle()
    {
        CartLog::create($this->logData);
        logger('Job started processing for user: ' . $this->logData['user_id']);

        logger()->info('Full Data:', $this->logData);
    }
}
