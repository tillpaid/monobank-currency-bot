<?php

namespace App\Services\Interfaces;

interface TelegramServiceInterface
{
    public function processWebhook(array $data): void;
}
