<?php

namespace App\Telegram\Processes\ProcessState\Interfaces;

use Illuminate\Database\Eloquent\Model;

interface ProcessTelegramStateInterface
{
    public function process(Model $user, string $messageText): string;
}
