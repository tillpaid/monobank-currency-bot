<?php

declare(strict_types=1);

namespace App\Services\Models;

use App\Models\TelegramUser;
use App\Repositories\TelegramUserRepository;
use Illuminate\Database\Eloquent\Collection;

class TelegramUserService
{
    private TelegramUserRepository $telegramUserRepository;

    public function __construct(TelegramUserRepository $telegramUserRepository)
    {
        $this->telegramUserRepository = $telegramUserRepository;
    }

    public function getByChatId(string $chatId): ?TelegramUser
    {
        return $this->telegramUserRepository->getByChatId($chatId);
    }

    public function createIfNotExists(string $chatId): void
    {
        $this->telegramUserRepository->createIfNotExists($chatId);
    }

    public function updateState(TelegramUser $telegramUser, ?string $state, ?array $stateAdditional): bool
    {
        $telegramUser->state = $state;

        if ($stateAdditional) {
            $this->updateStateAdditional($telegramUser, $stateAdditional);
        }

        return $telegramUser->save();
    }

    public function updateStateAdditional(TelegramUser $telegramUser, ?array $stateAdditional): bool
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
