<?php

declare(strict_types=1);

namespace App\Telegram\Processes;

use App\Models\TelegramUser;
use App\Services\Models\TelegramUserService;
use App\Services\Telegram\TelegramBotService;

class ProcessTelegramCommand
{
    private const COMMAND_START = '/start';
    private const COMMAND_PING = '/ping';
    private const COMMAND_ENV = '/env';
    private const COMMAND_REPORT = '/report';

    public function __construct(
        protected TelegramUserService $telegramUserService,
        protected TelegramBotService $telegramBotService,
    ) {}

    public function process(TelegramUser $telegramUser, string $messageText): string
    {
        return match ($messageText) {
            self::COMMAND_START => $this->processStart($telegramUser),
            self::COMMAND_PING => $this->processPing(),
            self::COMMAND_ENV => $this->processEnv(),
            self::COMMAND_REPORT => $this->processReport($telegramUser),
            default => __('telegram.commandNotFound'),
        };
    }

    private function processStart(TelegramUser $telegramUser): string
    {
        $this->telegramUserService->updateState($telegramUser, null, null);

        return __('telegram.startMessage');
    }

    private function processPing(): string
    {
        return __('telegram.pong');
    }

    private function processEnv(): string
    {
        return __('telegram.environment', ['env' => config('app.env')]);
    }

    private function processReport(TelegramUser $telegramUser): string
    {
        return $this->telegramBotService->buildUserReport($telegramUser->getId());
    }
}
