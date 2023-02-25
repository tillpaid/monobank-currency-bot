<?php

namespace App\Services\Interfaces\Telegram;

use Longman\TelegramBot\Telegram;

interface TelegramBotServiceInterface
{
    public function sendMessage(string $chatId, string $message): void;

    public function getBot(): Telegram;

    public function getMyId(): string;

    public function buildUserBalanceMessage(int $userId): string;

    public function buildUserReport(int $userId): string;

    public function format(float $number, int $decimals = 2, $trim = true): string;
}
