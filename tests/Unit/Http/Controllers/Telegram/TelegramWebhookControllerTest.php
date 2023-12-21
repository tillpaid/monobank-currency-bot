<?php

declare(strict_types=1);

namespace Tests\Unit\Http\Controllers\Telegram;

use App\Services\Telegram\TelegramBotService;
use App\Services\Telegram\TelegramService;
use Longman\TelegramBot\Exception\TelegramException;
use Longman\TelegramBot\Telegram;
use Mockery\MockInterface;
use Tests\TestCase;

class TelegramWebhookControllerTest extends TestCase
{
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

    public function testCatchWebhookSuccess(): void
    {
        $requestData = ['update_id' => $this->faker->randomNumber()];

        $this->telegramBotService
            ->shouldReceive('getBot')
            ->once()
            ->andReturn($this->telegram);

        $this->telegram
            ->shouldReceive('useGetUpdatesWithoutDatabase')
            ->once();

        $this->telegram
            ->shouldReceive('handle')
            ->once()
            ->andReturn(true);

        $this->telegramService
            ->shouldReceive('processWebhook')
            ->once()
            ->with($requestData);

        $response = $this->postJson('/telegram-webhook', $requestData);
        $response->assertJson(['success' => true]);
    }

    public function testNoServerResponse(): void
    {
        $this->telegramBotService
            ->shouldReceive('getBot')
            ->once()
            ->andReturn($this->telegram);

        $this->telegram
            ->shouldReceive('useGetUpdatesWithoutDatabase')
            ->once();

        $this->telegram
            ->shouldReceive('handle')
            ->once()
            ->andReturn(false);

        $this->telegramService
            ->shouldReceive('processWebhook')
            ->never();

        $response = $this->postJson('/telegram-webhook');
        $response->assertJson(['success' => true]);
    }

    public function testException(): void
    {
        $exceptionMessage = 'Test exception message';

        $this->telegramBotService
            ->shouldReceive('getBot')
            ->once()
            ->andReturn($this->telegram);

        $this->telegram
            ->shouldReceive('useGetUpdatesWithoutDatabase')
            ->once();

        $this->telegram
            ->shouldReceive('handle')
            ->once()
            ->andThrow(new TelegramException($exceptionMessage));

        $this->telegramService
            ->shouldReceive('processWebhook')
            ->never();

        $response = $this->postJson('/telegram-webhook');
        $response->assertJson(['success' => false, 'error' => $exceptionMessage]);
    }
}