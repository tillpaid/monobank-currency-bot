<?php

namespace App\Repositories;

use App\Models\TelegramUser;
use App\Repositories\Interfaces\TelegramUserRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

/**
 * Class TelegramUserRepository
 * @package App\Repositories
 */
class TelegramUserRepository implements TelegramUserRepositoryInterface
{
    /**
     * @var TelegramUser
     */
    private $model;

    /**
     * TelegramUserRepository constructor.
     * @param TelegramUser $telegramUser
     */
    public function __construct(TelegramUser $telegramUser)
    {
        $this->model = $telegramUser;
    }

    /**
     * @param string $chatId
     * @return Model|null
     */
    public function getByChatId(string $chatId): ?Model
    {
        return $this->model->where('chat_id', $chatId)->first();
    }

    /**
     * @param string $chatId
     */
    public function createIfNotExists(string $chatId): void
    {
        if (!$this->model->where('chat_id', $chatId)->count()) {
            $this->model->create(['chat_id' => $chatId]);
        }
    }

    /**
     * @return Collection|null
     */
    public function all(): ?Collection
    {
        return $this->model->all();
    }
}
