<?php

namespace App\Services\Telegram;

use App\Services\Models\TelegramUserService;
use App\Telegram\ProcessTelegramRequest;

class TelegramService
{
    private TelegramBotService $telegramBotService;
    private TelegramUserService $telegramUserService;
    private ProcessTelegramRequest $processTelegramRequest;

    public function __construct(
        TelegramBotService $telegramBotService,
        TelegramUserService $telegramUserService,
        ProcessTelegramRequest $processTelegramRequest
    ) {
        $this->telegramBotService = $telegramBotService;
        $this->telegramUserService = $telegramUserService;
        $this->processTelegramRequest = $processTelegramRequest;
    }

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

    public function sendMessageAboutChangeEnv(): void
    {
        $chatId = $this->telegramBotService->getMyId();
        $message = __('telegram.environmentChanged', ['env' => config('app.env')]);

        $this->telegramBotService->sendMessage($chatId, $message);
    }

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

    private function checkAuth($chatId): bool
    {
        return $chatId == $this->telegramBotService->getMyId();
    }
}
