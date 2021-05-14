<?php

namespace App\Services\Interfaces\Telegram;

use Longman\TelegramBot\Telegram;

/**
 * Interface TelegramBotServiceInterface
 * @package App\Services\Interfaces\Telegram
 */
interface TelegramBotServiceInterface
{
    /**
     * @param string $chatId
     * @param string $message
     * @return void
     */
    public function sendMessage(string $chatId, string $message): void;

    /**
     * @return Telegram
     */
    public function getBot(): Telegram;

    /**
     * @return string
     */
    public function getMyId(): string;

    /**
     * @param int $userId
     * @return string
     */
    public function buildUserBalanceMessage(int $userId): string;

    /**
     * @param int $userId
     * @return string
     */
    public function buildUserReport(int $userId): string;
}
