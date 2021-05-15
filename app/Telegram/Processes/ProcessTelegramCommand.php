<?php

namespace App\Telegram\Processes;

use App\Services\Interfaces\Models\TelegramUserServiceInterface;
use App\Services\Interfaces\Telegram\TelegramBotServiceInterface;
use Illuminate\Database\Eloquent\Model;

/**
 * Class ProcessTelegramCommand
 * @package App\Telegram\Processes
 */
class ProcessTelegramCommand
{
    /**
     * @var TelegramUserServiceInterface
     */
    protected $telegramUserService;
    /**
     * @var TelegramBotServiceInterface
     */
    protected $telegramBotService;

    /**
     * ProcessTelegramCommand constructor.
     * @param TelegramUserServiceInterface $telegramUserService
     * @param TelegramBotServiceInterface $telegramBotService
     * @return void
     */
    public function __construct(
        TelegramUserServiceInterface $telegramUserService,
        TelegramBotServiceInterface $telegramBotService
    )
    {
        $this->telegramUserService = $telegramUserService;
        $this->telegramBotService = $telegramBotService;
    }

    /**
     * @param Model $user
     * @param string $messageText
     * @return string
     */
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

    /**
     * @param Model $user
     * @param string|null $state
     * @param array|null $stateAdditional
     * @return bool
     */
    private function updateUserState(Model $user, ?string $state, ?array $stateAdditional = null): bool
    {
        return $this->telegramUserService->updateState($user, $state, $stateAdditional);
    }
}
