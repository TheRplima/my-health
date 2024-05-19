<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Http\Controllers\TelegramController;

class PollTelegramUpdates extends Command
{
    protected $signature = 'telegram:poll-updates';
    protected $description = 'Poll Telegram for new updates';

    protected $telegramController;

    public function __construct(TelegramController $telegramController)
    {
        parent::__construct();
        $this->telegramController = $telegramController;
    }

    public function handle()
    {
        $this->telegramController->handleUpdates();
    }
}
