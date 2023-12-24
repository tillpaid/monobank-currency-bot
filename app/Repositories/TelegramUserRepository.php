<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Models\TelegramUser;
use Illuminate\Database\Eloquent\Collection;

class TelegramUserRepository
{
    private TelegramUser $model;

    public function __construct(TelegramUser $telegramUser)
    {
        $this->model = $telegramUser;
    }

    public function getByChatId(string $chatId): ?TelegramUser
    {
        return $this->model->where('chat_id', $chatId)->first();
    }

    public function createIfNotExists(string $chatId): void
    {
        if (!$this->model->where('chat_id', $chatId)->count()) {
            $this->model->create(['chat_id' => $chatId]);
        }
    }

    /**
     * @return null|Collection|TelegramUser[]
     */
    public function all(): null|Collection|array
    {
        return $this->model->all();
    }
}
