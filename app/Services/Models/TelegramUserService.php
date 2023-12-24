<?php

declare(strict_types=1);

namespace App\Services\Models;

use App\Repositories\TelegramUserRepository;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

class TelegramUserService
{
    private TelegramUserRepository $telegramUserRepository;

    public function __construct(TelegramUserRepository $telegramUserRepository)
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

    public function updateState(Model $telegramUser, ?string $state, ?array $stateAdditional): bool
    {
        $telegramUser->state = $state;

        if ($stateAdditional) {
            $this->updateStateAdditional($telegramUser, $stateAdditional);
        }

        return $telegramUser->save();
    }

    public function updateStateAdditional(Model $telegramUser, ?array $stateAdditional): bool
    {
        if ($telegramUser->state_additional) {
            $telegramUser->state_additional = array_merge($telegramUser->state_additional, $stateAdditional);
        } else {
            $telegramUser->state_additional = $stateAdditional;
        }

        return $telegramUser->save();
    }

    public function all(): ?Collection
    {
        return $this->telegramUserRepository->all();
    }
}
