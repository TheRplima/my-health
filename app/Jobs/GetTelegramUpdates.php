<?php

namespace App\Jobs;

use App\Http\Resources\TelegramUpdateCollection;
use App\Http\Resources\TelegramUpdateResource;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Cache;
use NotificationChannels\Telegram\TelegramUpdates;

class GetTelegramUpdates implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
    public function __construct()
    {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $storageUpdates = new TelegramUpdateCollection(Cache::get('telegram_updates') ?? []);
        $storageUpdateIds = Cache::get('telegram_storage_update_ids') ?? [];

        $updates = TelegramUpdates::create()
            ->latest()
            ->options([
                'timeout' => 0,
                'allowed_updates' => "callback_query,message"
            ])
            ->get();

        if ($updates['ok'] && count($updates['result']) > 0) {
            foreach ($updates['result'] as $update) {
                $updateObj = new TelegramUpdateResource($update);
                $updateId = $update['update_id'];
                if (in_array($updateId, $storageUpdateIds)) {
                    continue;
                }
                $storageUpdates->push($updateObj);
                $storageUpdateIds[] = $updateId;
            }
            Cache::put('telegram_storage_update_ids', $storageUpdateIds);
            Cache::put('telegram_updates', $storageUpdates);
        }
    }
}
