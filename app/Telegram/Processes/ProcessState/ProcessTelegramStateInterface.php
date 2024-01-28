<?php

declare(strict_types=1);

namespace App\Telegram\Processes\ProcessState;

use App\Models\TelegramUser;

interface ProcessTelegramStateInterface
{
    public function getState(): ?string;

    public function process(TelegramUser $telegramUser, string $messageText): string;
}
