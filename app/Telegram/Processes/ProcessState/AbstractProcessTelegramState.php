<?php

namespace App\Telegram\Processes\ProcessState;

use App\Services\Interfaces\Models\TelegramUserServiceInterface;
use App\Telegram\Processes\ProcessState\Interfaces\ProcessTelegramStateInterface;
use Illuminate\Database\Eloquent\Model;

abstract class AbstractProcessTelegramState implements ProcessTelegramStateInterface
{
    protected $telegramUserService;

    public function __construct(TelegramUserServiceInterface $telegramUserService)
    {
        $this->telegramUserService = $telegramUserService;
    }

    final protected function updateUserState(Model $user, ?string $state): bool
    {
        return $this->telegramUserService->updateState($user, $state);
    }
}
