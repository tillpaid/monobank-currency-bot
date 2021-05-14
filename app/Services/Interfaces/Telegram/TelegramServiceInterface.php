<?php

namespace App\Services\Interfaces\Telegram;

/**
 * Interface TelegramServiceInterface
 * @package App\Services\Interfaces\Telegram
 */
interface TelegramServiceInterface
{
    /**
     * @param array $data
     * @return void
     */
    public function processWebhook(array $data): void;

    /**
     * @return void
     */
    public function sendMessageAboutChangeEnv(): void;
}
