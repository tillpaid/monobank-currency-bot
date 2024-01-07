<?php

declare(strict_types=1);

namespace App\Services\Telegram;

use App\Repositories\TelegramUserRepository;
use App\Telegram\ProcessTelegramRequest;
use Longman\TelegramBot\Exception\TelegramException;

class TelegramService
{
    public function __construct(
        private readonly TelegramBotService $telegramBotService,
        private readonly TelegramUserRepository $telegramUserRepository,
        private readonly ProcessTelegramRequest $processTelegramRequest,
    ) {}

    /**
     * @param array<string, mixed> $data
     */
    public function processWebhook(array $data): void
    {
        $message = array_key_exists('edited_message', $data)
            ? $data['edited_message']
            : $data['message'];

        $chatId = (string) $message['chat']['id'];
        $messageText = $message['text'];

        $this->telegramUserRepository->createIfNotExists($chatId);
        $this->processMessage($chatId, $messageText);
    }

    /**
     * @throws TelegramException
     */
    public function sendMessageAboutChangeEnv(): void
    {
        $chatId = $this->telegramBotService->getMyId();
        $message = __('telegram.environmentChanged', ['env' => config('app.env')]);

        $this->telegramBotService->sendMessage($chatId, $message);
    }

    private function processMessage(string $chatId, string $messageText): void
    {
        if (!$this->checkAuth($chatId)) {
            $this->telegramBotService->sendMessage($chatId, __('telegram.notAuth'));

            return;
        }

        $telegramUser = $this->telegramUserRepository->getByChatId($chatId);
        $responseMessage = $this->processTelegramRequest->process($telegramUser, $messageText);

        $this->telegramBotService->sendMessage($chatId, $responseMessage);
    }

    private function checkAuth(string $chatId): bool
    {
        return $chatId === $this->telegramBotService->getMyId();
    }
}
