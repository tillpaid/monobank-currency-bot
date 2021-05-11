<?php

namespace App\Services\Interfaces\Models;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

interface TelegramUserServiceInterface
{
    public function getByChatId(string $chatId): ?Model;

    public function createIfNotExists(string $chatId): void;

    public function updateState(Model $telegramUser, ?string $state, ?array $stateAdditional): bool;

    public function updateStateAdditional(Model $telegramUser, ?array $stateAdditional): bool;

    public function all(): ?Collection;
}
