<?php

declare(strict_types=1);

namespace App\Telegram;

use App\Models\TelegramUser;
use App\Telegram\Processes\ProcessTelegramCommand;
use App\Telegram\Processes\ProcessTelegramState;
use Exception;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;

class ProcessTelegramRequest
{
    private ProcessTelegramCommand $processTelegramCommand;
    private ProcessTelegramState $processTelegramState;

    public function __construct(
        ProcessTelegramCommand $processTelegramCommand,
        ProcessTelegramState $processTelegramState
    ) {
        $this->processTelegramCommand = $processTelegramCommand;
        $this->processTelegramState = $processTelegramState;
    }

    public function process(TelegramUser $telegramUser, string $messageText): string
    {
        try {
            return $this->isCommand($messageText)
                ? $this->processTelegramCommand->process($telegramUser, $messageText)
                : $this->processTelegramState->process($telegramUser, $messageText);
        } catch (Exception $exception) {
            Log::error('Telegram request error: ');
            Log::error($exception);

            return __('telegram.internalError');
        }
    }

    private function isCommand(string $messageText): bool
    {
        return '/' === substr($messageText, 0, 1);
    }
}
