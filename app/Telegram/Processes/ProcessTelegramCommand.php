<?php

namespace App\Telegram\Processes;

use App\Services\Models\TelegramUserService;
use App\Services\Telegram\TelegramBotService;
use Illuminate\Database\Eloquent\Model;

class ProcessTelegramCommand
{
    protected TelegramUserService $telegramUserService;
    protected TelegramBotService $telegramBotService;

    public function __construct(
        TelegramUserService $telegramUserService,
        TelegramBotService $telegramBotService
    ) {
        $this->telegramUserService = $telegramUserService;
        $this->telegramBotService = $telegramBotService;
    }

    public function process(Model $user, string $messageText): string
    {
        switch ($messageText) {
            case '/start':
                $this->updateUserState($user, null);
                $responseMessage = __('telegram.startMessage');

                break;
            case '/env':
                $responseMessage = __('telegram.environment', ['env' => config('app.env')]);
                break;
            case '/report':
                $responseMessage = $this->telegramBotService->buildUserReport($user->id);
                break;
            default:
                $responseMessage = __('telegram.commandNotFound');
        }

        return $responseMessage;
    }

    private function updateUserState(Model $user, ?string $state, ?array $stateAdditional = null): bool
    {
        return $this->telegramUserService->updateState($user, $state, $stateAdditional);
    }
}
