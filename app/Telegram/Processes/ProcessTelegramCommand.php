<?php

declare(strict_types=1);

namespace App\Telegram\Processes;

use App\Models\TelegramUser;
use App\Services\Models\TelegramUserService;
use App\Services\Telegram\TelegramBotService;

class ProcessTelegramCommand
{
    public function __construct(
        protected TelegramUserService $telegramUserService,
        protected TelegramBotService $telegramBotService,
    ) {
    }

    public function process(TelegramUser $telegramUser, string $messageText): string
    {
        switch ($messageText) {
            case '/start':
                $this->updateUserState($telegramUser, null);
                $responseMessage = __('telegram.startMessage');

                break;

            case '/ping':
                $responseMessage = __('telegram.pong');

                break;

            case '/env':
                $responseMessage = __('telegram.environment', ['env' => config('app.env')]);

                break;

            case '/report':
                $responseMessage = $this->telegramBotService->buildUserReport($telegramUser->getId());

                break;

            default:
                $responseMessage = __('telegram.commandNotFound');
        }

        return $responseMessage;
    }

    /**
     * @param null|array<string, float|string> $stateAdditional
     */
    private function updateUserState(TelegramUser $telegramUser, ?string $state, ?array $stateAdditional = null): bool
    {
        return $this->telegramUserService->updateState($telegramUser, $state, $stateAdditional);
    }
}
