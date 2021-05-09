<?php

namespace App\Services\Interfaces\Models;

use Illuminate\Database\Eloquent\Model;

interface TelegramUserServiceInterface
{
    public function getByChatId(string $chatId): ?Model;

    public function createIfNotExists(string $chatId): void;
}
