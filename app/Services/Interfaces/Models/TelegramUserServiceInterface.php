<?php

namespace App\Services\Interfaces\Models;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

/**
 * Interface TelegramUserServiceInterface
 * @package App\Services\Interfaces\Models
 */
interface TelegramUserServiceInterface
{
    /**
     * @param string $chatId
     * @return Model|null
     */
    public function getByChatId(string $chatId): ?Model;

    /**
     * @param string $chatId
     * @return void
     */
    public function createIfNotExists(string $chatId): void;

    /**
     * @param Model $telegramUser
     * @param string|null $state
     * @param array|null $stateAdditional
     * @return bool
     */
    public function updateState(Model $telegramUser, ?string $state, ?array $stateAdditional): bool;

    /**
     * @param Model $telegramUser
     * @param array|null $stateAdditional
     * @return bool
     */
    public function updateStateAdditional(Model $telegramUser, ?array $stateAdditional): bool;

    /**
     * @return Collection|null
     */
    public function all(): ?Collection;
}
