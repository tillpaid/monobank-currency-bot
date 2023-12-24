<?php

declare(strict_types=1);

namespace Tests\Unit\Commands\Telegram;

use App\Services\Telegram\TelegramBotService;
use App\Services\Telegram\TelegramService;
use Longman\TelegramBot\Entities\ServerResponse;
use Longman\TelegramBot\Exception\TelegramException;
use Longman\TelegramBot\Telegram;
use Mockery\MockInterface;
use Tests\TestCase;

class SetWebhookTest extends TestCase
{
    private const COMMAND_NAME = 'telegram:set-webhook';

    private Telegram|MockInterface $telegram;
    private TelegramService|MockInterface $telegramService;
    private TelegramBotService|MockInterface $telegramBotService;

    protected function setUp(): void
    {
        parent::setUp();

        $this->telegram = $this->mock(Telegram::class);
        $this->telegramService = $this->mock(TelegramService::class);
        $this->telegramBotService = $this->mock(TelegramBotService::class);
    }

    public function testSuccess(): void
    {
        $resultDescription = 'Test description';

        $this->telegramBotService
            ->shouldReceive('getBot')
            ->once()
            ->andReturn($this->telegram)
        ;

        $this->telegram
            ->shouldReceive('setWebhook')
            ->once()
            ->andReturn(new ServerResponse(['ok' => true, 'description' => $resultDescription]))
        ;

        $this->telegramService
            ->shouldReceive('sendMessageAboutChangeEnv')
            ->once()
        ;

        $this->runCommand([$resultDescription]);
    }

    public function testNotSuccess(): void
    {
        $resultDescription = 'Test description';

        $this->telegramBotService
            ->shouldReceive('getBot')
            ->once()
            ->andReturn($this->telegram)
        ;

        $this->telegram
            ->shouldReceive('setWebhook')
            ->once()
            ->andReturn(new ServerResponse(['ok' => false, 'description' => $resultDescription]))
        ;

        $this->telegramService
            ->shouldReceive('sendMessageAboutChangeEnv')
            ->never()
        ;

        $this->runCommand([$resultDescription], false);
    }

    public function testExceptionFromSetWebhook(): void
    {
        $exceptionMessage = 'Test exception message';

        $this->telegramBotService
            ->shouldReceive('getBot')
            ->once()
            ->andReturn($this->telegram)
        ;

        $this->telegram
            ->shouldReceive('setWebhook')
            ->once()
            ->andThrow(new TelegramException($exceptionMessage))
        ;

        $this->telegramService
            ->shouldReceive('sendMessageAboutChangeEnv')
            ->never()
        ;

        $this->runCommand([$exceptionMessage]);
    }

    public function testExceptionFromSendMessage(): void
    {
        $resultDescription = 'Test description';
        $exceptionMessage = 'Test exception message';

        $this->telegramBotService
            ->shouldReceive('getBot')
            ->once()
            ->andReturn($this->telegram)
        ;

        $this->telegram
            ->shouldReceive('setWebhook')
            ->once()
            ->andReturn(new ServerResponse(['ok' => true, 'description' => $resultDescription]))
        ;

        $this->telegramService
            ->shouldReceive('sendMessageAboutChangeEnv')
            ->once()
            ->andThrow(new TelegramException($exceptionMessage))
        ;

        $this->runCommand([$resultDescription, $exceptionMessage]);
    }

    /**
     * @param string[] $expectedOutput
     */
    private function runCommand(array $expectedOutput, bool $expect = true): void
    {
        $command = $this->artisan(self::COMMAND_NAME);

        foreach ($expectedOutput as $output) {
            $expect
                ? $command->expectsOutput($output)
                : $command->doesntExpectOutput($output);
        }
    }
}
