<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Models\TelegramUser;

class TelegramUserRepository
{
    public function __construct(
        private TelegramUser $telegramUser,
    ) {}

    /**
     * @return TelegramUser[]
     */
    public function findAll(): array
    {
        // @phpstan-ignore-next-line
        return $this->telegramUser->newQuery()->get()->all();
    }

    public function getByChatId(string $chatId): ?TelegramUser
    {
        return $this->telegramUser->newQuery()->where('chat_id', $chatId)->first();
    }

    // TODO: Repository should not perform such actions. Refactor it.
    public function createIfNotExists(string $chatId): void
    {
        $telegramUserCount = $this->telegramUser->newQuery()->where('chat_id', $chatId)->count();

        if (0 === $telegramUserCount) {
            $this->telegramUser->newQuery()->create(['chat_id' => $chatId]);
        }
    }
}
