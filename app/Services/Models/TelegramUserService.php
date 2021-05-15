<?php

namespace App\Services\Models;

use App\Repositories\Interfaces\TelegramUserRepositoryInterface;
use App\Services\Interfaces\Models\TelegramUserServiceInterface;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

/**
 * Class TelegramUserService
 * @package App\Services\Models
 */
class TelegramUserService implements TelegramUserServiceInterface
{
    /**
     * @var TelegramUserRepositoryInterface
     */
    private $telegramUserRepository;

    /**
     * TelegramUserService constructor.
     * @param TelegramUserRepositoryInterface $telegramUserRepository
     */
    public function __construct(TelegramUserRepositoryInterface $telegramUserRepository)
    {
        $this->telegramUserRepository = $telegramUserRepository;
    }

    /**
     * @param string $chatId
     * @return Model|null
     */
    public function getByChatId(string $chatId): ?Model
    {
        return $this->telegramUserRepository->getByChatId($chatId);
    }

    /**
     * @param string $chatId
     * @return void
     */
    public function createIfNotExists(string $chatId): void
    {
        $this->telegramUserRepository->createIfNotExists($chatId);
    }

    /**
     * @param Model $telegramUser
     * @param string|null $state
     * @param array|null $stateAdditional
     * @return bool
     */
    public function updateState(Model $telegramUser, ?string $state, ?array $stateAdditional): bool
    {
        $telegramUser->state = $state;

        if ($stateAdditional) {
            $this->updateStateAdditional($telegramUser, $stateAdditional);
        }

        return $telegramUser->save();
    }

    /**
     * @param Model $telegramUser
     * @param array|null $stateAdditional
     * @return bool
     */
    public function updateStateAdditional(Model $telegramUser, ?array $stateAdditional): bool
    {
        if ($telegramUser->state_additional) {
            $telegramUser->state_additional = array_merge($telegramUser->state_additional, $stateAdditional);
        } else {
            $telegramUser->state_additional = $stateAdditional;
        }

        return $telegramUser->save();
    }

    /**
     * @return Collection|null
     */
    public function all(): ?Collection
    {
        return $this->telegramUserRepository->all();
    }
}
