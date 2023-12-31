<?php

declare(strict_types=1);

namespace App\Telegram;

use App\Models\TelegramUser;
use App\Telegram\Processes\ProcessTelegramCommand;
use App\Telegram\Processes\ProcessTelegramState;
use Exception;
use Illuminate\Support\Facades\Log;

class ProcessTelegramRequest
{
    public function __construct(
        private ProcessTelegramCommand $processTelegramCommand,
        private ProcessTelegramState $processTelegramState,
    ) {
    }

    public function process(TelegramUser $telegramUser, string $messageText): string
    {
        try {
            return $this->isCommand($messageText)
                ? $this->processTelegramCommand->process($telegramUser, $messageText)
                : $this->processTelegramState->process($telegramUser, $messageText);
        } catch (Exception $exception) {
            Log::error(sprintf('Telegram request exception: %s', $exception->getMessage()), [$exception]);

            return __('telegram.internalError');
        }
    }

    private function isCommand(string $messageText): bool
    {
        return '/' === substr($messageText, 0, 1);
    }
}
