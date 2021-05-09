<?php

namespace App\Services\Models;

use App\Repositories\Interfaces\TelegramUserRepositoryInterface;
use App\Services\Interfaces\Models\TelegramUserServiceInterface;
use Illuminate\Database\Eloquent\Model;

class TelegramUserService implements TelegramUserServiceInterface
{
    private $telegramUserRepository;

    public function __construct(TelegramUserRepositoryInterface $telegramUserRepository)
    {
        $this->telegramUserRepository = $telegramUserRepository;
    }

    public function getByChatId(string $chatId): ?Model
    {
        return $this->telegramUserRepository->getByChatId($chatId);
    }

    public function createIfNotExists(string $chatId): void
    {
        $this->telegramUserRepository->createIfNotExists($chatId);
    }

    public function updateState(Model $telegramUser, ?string $state): bool
    {
        $telegramUser->state = $state;
        return $telegramUser->save();
    }
}
