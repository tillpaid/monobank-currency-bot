<?php

namespace App\Services\Interfaces\Telegram;

interface TelegramServiceInterface
{
    public function processWebhook(array $data): void;

    public function sendMessageAboutChangeEnv(): void;
}
