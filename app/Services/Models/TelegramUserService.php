<?php

declare(strict_types=1);

namespace App\Services\Models;

use App\Models\TelegramUser;

class TelegramUserService
{
    // TODO: Will be changed to setter in entity
    public function updateState(TelegramUser $telegramUser, ?string $state = null, ?array $stateAdditional = null): bool
    {
        $telegramUser->setState($state);

        if ($stateAdditional) {
            $this->updateStateAdditional($telegramUser, $stateAdditional);
        }

        return $telegramUser->save();
    }

    public function updateStateAdditional(TelegramUser $telegramUser, ?array $stateAdditional): bool
    {
        if ($telegramUser->getStateAdditional()) {
            $telegramUser->setStateAdditional(array_merge($telegramUser->getStateAdditional(), $stateAdditional));
        } else {
            $telegramUser->setStateAdditional($stateAdditional);
        }

        return $telegramUser->save();
    }
}
