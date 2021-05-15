<?php

namespace App\Telegram;

use App\Telegram\Processes\ProcessTelegramCommand;
use App\Telegram\Processes\ProcessTelegramState;
use Illuminate\Database\Eloquent\Model;

/**
 * Class ProcessTelegramRequest
 * @package App\Telegram
 */
class ProcessTelegramRequest
{
    /**
     * @var ProcessTelegramCommand
     */
    private $processTelegramCommand;
    /**
     * @var ProcessTelegramState
     */
    private $processTelegramState;

    /**
     * ProcessTelegramRequest constructor.
     * @param ProcessTelegramCommand $processTelegramCommand
     * @param ProcessTelegramState $processTelegramState
     * @return void
     */
    public function __construct(ProcessTelegramCommand $processTelegramCommand, ProcessTelegramState $processTelegramState)
    {
        $this->processTelegramCommand = $processTelegramCommand;
        $this->processTelegramState = $processTelegramState;
    }

    /**
     * @param Model $user
     * @param string $messageText
     * @return string
     */
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

    /**
     * @param string $messageText
     * @return bool
     */
    private function isCommand(string $messageText): bool
    {
        return substr($messageText, 0, 1) == '/';
    }
}
