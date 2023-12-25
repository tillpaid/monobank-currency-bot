<?php

declare(strict_types=1);

namespace Tests\Mocks;

use App\Services\Telegram\TelegramBotService;

class TelegramBotServiceMock extends TelegramBotService
{
    /**
     * @var array<string, string[]>
     */
    private array $sentMessages = [];

    public function sendMessage(string $chatId, string $message): void
    {
        if (!isset($this->sentMessages[$chatId])) {
            $this->sentMessages[$chatId] = [];
        }

        $this->sentMessages[$chatId][] = $message;
    }

    /**
     * @return string[]
     */
    public function getAndResetMyMessages(): array
    {
        $messages = $this->sentMessages[$this->getMyId()] ?? [];
        $this->sentMessages[$this->getMyId()] = [];

        return $messages;
    }
}
