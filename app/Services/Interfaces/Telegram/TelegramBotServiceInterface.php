<?php

namespace App\Services\Interfaces\Telegram;

use Longman\TelegramBot\Telegram;

interface TelegramBotServiceInterface
{
    public function sendMessage(string $chatId, string $message, array $keyboard = []): void;

    public function getBot(): Telegram;

    public function getMyId(): string;
}
