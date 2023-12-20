<?php

declare(strict_types=1);

namespace Tests\Unit\Commands\Telegram;

use App\Services\Telegram\TelegramBotService;
use Longman\TelegramBot\Entities\ServerResponse;
use Longman\TelegramBot\Exception\TelegramException;
use Longman\TelegramBot\Telegram;
use Mockery\MockInterface;
use Tests\TestCase;

class UnsetWebhookTest extends TestCase
{
    private const COMMAND_NAME = 'telegram:unset-webhook';

    private Telegram|MockInterface $telegram;
    private TelegramBotService|MockInterface $telegramBotService;

    protected function setUp(): void
    {
        parent::setUp();

        $this->telegram = $this->mock(Telegram::class);
        $this->telegramBotService = $this->mock(TelegramBotService::class);
    }

    public function testSuccess(): void
    {
        $resultDescription = 'Test description';

        $this->telegramBotService
            ->shouldReceive('getBot')
            ->once()
            ->andReturn($this->telegram);

        $this->telegram
            ->shouldReceive('deleteWebhook')
            ->once()
            ->andReturn(new ServerResponse(['ok' => true, 'description' => $resultDescription]));

        $this->runCommand($resultDescription);
    }

    public function testNotSuccess(): void
    {
        $resultDescription = 'Test description';

        $this->telegramBotService
            ->shouldReceive('getBot')
            ->once()
            ->andReturn($this->telegram);

        $this->telegram
            ->shouldReceive('deleteWebhook')
            ->once()
            ->andReturn(new ServerResponse(['ok' => false, 'description' => $resultDescription]));

        $this->runCommand($resultDescription, false);
    }

    public function testExceptionFromSetWebhook(): void
    {
        $exceptionMessage = 'Test exception message';

        $this->telegramBotService
            ->shouldReceive('getBot')
            ->once()
            ->andReturn($this->telegram);

        $this->telegram
            ->shouldReceive('deleteWebhook')
            ->once()
            ->andThrow(new TelegramException($exceptionMessage));

        $this->runCommand($exceptionMessage);
    }

    private function runCommand(string $expectedOutput, bool $expect = true): void
    {
        $command = $this->artisan(self::COMMAND_NAME);
        $expect
            ? $command->expectsOutput($expectedOutput)
            : $command->doesntExpectOutput($expectedOutput);
    }
}
