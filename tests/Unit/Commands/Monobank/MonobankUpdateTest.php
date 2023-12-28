<?php

declare(strict_types=1);

namespace Tests\Unit\Commands\Monobank;

use App\Models\TelegramUser;
use App\Services\Monobank\MonobankCurrencyService;
use App\Services\Telegram\TelegramBotService;
use Mockery\MockInterface;
use Tests\TestCase;

class MonobankUpdateTest extends TestCase
{
    private const COMMAND_NAME = 'monobank:update';

    private MonobankCurrencyService|MockInterface $monobankCurrencyService;
    private TelegramBotService|MockInterface $telegramBotService;

    protected function setUp(): void
    {
        parent::setUp();

        $this->monobankCurrencyService = $this->mock(MonobankCurrencyService::class);
        $this->telegramBotService = $this->mock(TelegramBotService::class);
    }

    public function testCurrencyRatesUpdated(): void
    {
        $telegramUsersCount = 5;
        $telegramUsers = array_map(
            fn (int $i) => $this->fixturesHelper->createTelegramUser((string) $i),
            range(1, $telegramUsersCount)
        );

        $expectedSendMessageCalls = array_map(
            fn (TelegramUser $telegramUser) => [
                'chat_id' => $telegramUser->getChatId(),
                'message' => sprintf('Report for user %d', $telegramUser->getId()),
            ],
            $telegramUsers
        );

        $this->monobankCurrencyService
            ->shouldReceive('updateCurrencyRates')
            ->times(1)
            ->andReturn(true)
        ;

        $this->telegramBotService
            ->shouldReceive('buildUserReport')
            ->times($telegramUsersCount)
            ->andReturnUsing(fn (int $userId) => sprintf('Report for user %d', $userId))
        ;

        $sendMessageCalls = [];
        $this->telegramBotService
            ->shouldReceive('sendMessage')
            ->times($telegramUsersCount)
            ->andReturnUsing(function (string $chatId, string $message) use (&$sendMessageCalls): void {
                $sendMessageCalls[] = ['chat_id' => $chatId, 'message' => $message];
            })
        ;

        $this->runCommand();
        $this->assertSame($expectedSendMessageCalls, $sendMessageCalls);
    }

    public function testCurrencyRatesNotUpdated(): void
    {
        $this->fixturesHelper->createTelegramUser();

        $this->monobankCurrencyService
            ->shouldReceive('updateCurrencyRates')
            ->times(1)
            ->andReturn(false)
        ;

        $this->telegramBotService
            ->shouldReceive('buildUserReport')
            ->never()
        ;

        $this->telegramBotService
            ->shouldReceive('sendMessage')
            ->never()
        ;

        $this->runCommand();
    }

    private function runCommand(): void
    {
        $this->artisan(self::COMMAND_NAME);
    }
}
