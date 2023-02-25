<?php

namespace App\Repositories\Interfaces;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

interface TelegramUserRepositoryInterface
{
    public function getByChatId(string $chatId): ?Model;

    public function createIfNotExists(string $chatId): void;

    public function all(): ?Collection;
}
