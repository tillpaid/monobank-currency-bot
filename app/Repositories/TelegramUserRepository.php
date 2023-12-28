<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Models\TelegramUser;

class TelegramUserRepository
{
    private TelegramUser $telegramUser;

    public function __construct(TelegramUser $telegramUser)
    {
        $this->telegramUser = $telegramUser;
    }

    /**
     * @return TelegramUser[]
     */
    public function findAll(): array
    {
        return $this->telegramUser->newQuery()->get()->all();
    }

    public function getByChatId(string $chatId): ?TelegramUser
    {
        return $this->telegramUser->newQuery()->where('chat_id', $chatId)->get()->first();
    }

    // TODO: Repository should not perform such actions. Refactor it.
    public function createIfNotExists(string $chatId): void
    {
        if (!$this->telegramUser->newQuery()->where('chat_id', $chatId)->count()) {
            $this->telegramUser->newQuery()->create(['chat_id' => $chatId]);
        }
    }
}
