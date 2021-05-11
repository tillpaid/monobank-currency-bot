<?php

namespace App\Telegram;

use App\Telegram\Processes\ProcessTelegramCommand;
use App\Telegram\Processes\ProcessTelegramState;
use Illuminate\Database\Eloquent\Model;

class ProcessTelegramRequest
{
    private $processTelegramCommand;
    private $processTelegramState;

    public function __construct(ProcessTelegramCommand $processTelegramCommand, ProcessTelegramState $processTelegramState)
    {
        $this->processTelegramCommand = $processTelegramCommand;
        $this->processTelegramState = $processTelegramState;
    }

    public function process(Model $user, string $messageText): string
    {
        try {
            return $this->isCommand($messageText)
                ? $this->processTelegramCommand->process($user, $messageText)
                : $this->processTelegramState->process($user, $messageText);
        } catch (\Exception $exception) {
            return __('telegram.internalError');
        }
    }

    private function isCommand(string $messageText): bool
    {
        return substr($messageText, 0, 1) == '/';
    }
}
