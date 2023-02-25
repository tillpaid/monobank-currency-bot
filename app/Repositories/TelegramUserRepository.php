<?php

namespace App\Repositories;

use App\Models\TelegramUser;
use App\Repositories\Interfaces\TelegramUserRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

class TelegramUserRepository implements TelegramUserRepositoryInterface
{
    private TelegramUser $model;

    public function __construct(TelegramUser $telegramUser)
    {
        $this->model = $telegramUser;
    }

    public function getByChatId(string $chatId): ?Model
    {
        return $this->model->where('chat_id', $chatId)->first();
    }

    public function createIfNotExists(string $chatId): void
    {
        if (!$this->model->where('chat_id', $chatId)->count()) {
            $this->model->create(['chat_id' => $chatId]);
        }
    }

    public function all(): ?Collection
    {
        return $this->model->all();
    }
}
