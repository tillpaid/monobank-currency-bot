<?php

namespace App\Services\Telegram;

use App\Services\Interfaces\Models\TelegramUserServiceInterface;
use App\Services\Interfaces\Telegram\TelegramBotServiceInterface;
use App\Services\Interfaces\Telegram\TelegramServiceInterface;
use App\Telegram\ProcessTelegramRequest;

/**
 * Class TelegramService
 * @package App\Services\Telegram
 */
class TelegramService implements TelegramServiceInterface
{
    /**
     * @var TelegramBotServiceInterface
     */
    private $telegramBotService;
    /**
     * @var TelegramUserServiceInterface
     */
    private $telegramUserService;
    /**
     * @var ProcessTelegramRequest
     */
    private $processTelegramRequest;

    /**
     * TelegramService constructor.
     * @param TelegramBotServiceInterface $telegramBotService
     * @param TelegramUserServiceInterface $telegramUserService
     * @param ProcessTelegramRequest $processTelegramRequest
     */
    public function __construct(
        TelegramBotServiceInterface $telegramBotService,
        TelegramUserServiceInterface $telegramUserService,
        ProcessTelegramRequest $processTelegramRequest
    )
    {
        $this->telegramBotService = $telegramBotService;
        $this->telegramUserService = $telegramUserService;
        $this->processTelegramRequest = $processTelegramRequest;
    }

    /**
     * @param array $data
     * @return void
     */
    public function processWebhook(array $data): void
    {
        $message = array_key_exists('edited_message', $data)
            ? $data['edited_message']
            : $data['message'];

        $chatId = $message['chat']['id'];
        $messageText = $message['text'];

        $this->telegramUserService->createIfNotExists($chatId);
        $this->processMessage($chatId, $messageText);
    }

    /**
     * @return void
     */
    public function sendMessageAboutChangeEnv(): void
    {
        $chatId = $this->telegramBotService->getMyId();
        $message = __('telegram.environmentChanged', ['env' => config('app.env')]);

        $this->telegramBotService->sendMessage($chatId, $message);
    }

    /**
     * @param $chatId
     * @param $messageText
     * @return void
     */
    private function processMessage($chatId, $messageText): void
    {
        if (!$this->checkAuth($chatId)) {
            $this->telegramBotService->sendMessage($chatId, __('telegram.notAuth'));
            return;
        }

        $user = $this->telegramUserService->getByChatId($chatId);
        $responseMessage = $this->processTelegramRequest->process($user, $messageText);

        $this->telegramBotService->sendMessage($chatId, $responseMessage);
    }

    /**
     * @param $chatId
     * @return bool
     */
    private function checkAuth($chatId): bool
    {
        return $chatId == $this->telegramBotService->getMyId();
    }
}
