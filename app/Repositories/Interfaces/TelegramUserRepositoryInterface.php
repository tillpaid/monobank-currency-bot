<?php

namespace App\Repositories\Interfaces;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

/**
 * Interface TelegramUserRepositoryInterface
 * @package App\Repositories\Interfaces
 */
interface TelegramUserRepositoryInterface
{
    /**
     * @param string $chatId
     * @return Model|null
     */
    public function getByChatId(string $chatId): ?Model;

    /**
     * @param string $chatId
     */
    public function createIfNotExists(string $chatId): void;

    /**
     * @return Collection|null
     */
    public function all(): ?Collection;
}
