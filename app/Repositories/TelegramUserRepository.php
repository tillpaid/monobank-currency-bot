<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Models\TelegramUser;
use Illuminate\Database\Eloquent\Collection;

class TelegramUserRepository
{
    private TelegramUser $telegramUser;

    public function __construct(TelegramUser $telegramUser)
    {
        $this->telegramUser = $telegramUser;
    }

    public function getByChatId(string $chatId): ?TelegramUser
    {
        return $this->telegramUser->newQuery()->where('chat_id', $chatId)->get()->first();
    }

    public function createIfNotExists(string $chatId): void
    {
        if (!$this->telegramUser->newQuery()->where('chat_id', $chatId)->count()) {
            $this->telegramUser->newQuery()->create(['chat_id' => $chatId]);
        }
    }

    /**
     * @return null|Collection|TelegramUser[]
     */
    public function all(): null|Collection|array
    {
        return $this->telegramUser->all();
    }
}
