<?php

declare(strict_types=1);

namespace Tests\Unit\Services\Models;

use App\Models\TelegramUser;
use App\Services\Models\TelegramUserService;
use Illuminate\Contracts\Container\BindingResolutionException;
use Tests\TestCase;

class TelegramUserServiceTest extends TestCase
{
    private TelegramUserService $telegramUserService;

    /**
     * @throws BindingResolutionException
     */
    protected function setUp(): void
    {
        parent::setUp();

        $this->telegramUserService = $this->app->make(TelegramUserService::class);
    }

    public function testGetByChatId(): void
    {
        $chatId = (string) random_int(1000, 10000);

        $expected = TelegramUser::factory()->create(['chat_id' => $chatId]);

        $result = $this->telegramUserService->getByChatId($chatId);

        $this->assertSame($expected->id, $result->id);
    }

    public function getCountByChatId(string $chatId): int
    {
        return TelegramUser::where('chat_id', $chatId)->count();
    }

    public function testCreateIfNotExists(): void
    {
        $chatId = (string) random_int(1000, 10000);

        $this->assertSame(0, $this->getCountByChatId($chatId));
        $this->telegramUserService->createIfNotExists($chatId);
        $this->assertSame(1, $this->getCountByChatId($chatId));
        $this->telegramUserService->createIfNotExists($chatId);
        $this->assertSame(1, $this->getCountByChatId($chatId));
    }

    public function testUpdateState(): void
    {
        $state = 'value';
        $telegramUser = TelegramUser::factory()->create();

        $this->assertNull($telegramUser->state);
        $this->telegramUserService->updateState($telegramUser, $state, []);
        $this->assertSame($state, $telegramUser->state);
    }

    public function testUpdateStateWithStateAdditional(): void
    {
        $state = 'value';
        $stateAdditional = ['key' => 'value'];
        $telegramUser = TelegramUser::factory()->create();

        $this->assertNull($telegramUser->state);
        $this->assertNull($telegramUser->state_additional);
        $this->telegramUserService->updateState($telegramUser, $state, $stateAdditional);
        $this->assertSame($state, $telegramUser->state);
        $this->assertSame($stateAdditional, $telegramUser->state_additional);
    }

    public function testUpdateStateAdditional(): void
    {
        $telegramUser = TelegramUser::factory()->create();
        $this->assertNull($telegramUser->state_additional);

        $stateAdditional = ['key' => 'value'];
        $this->telegramUserService->updateStateAdditional($telegramUser, $stateAdditional);
        $this->assertSame($stateAdditional, $telegramUser->state_additional);

        $stateAdditionalNew = ['key1' => 'value1'];
        $stateAdditionalResult = array_merge($stateAdditional, $stateAdditionalNew);

        $this->telegramUserService->updateStateAdditional($telegramUser, $stateAdditionalNew);
        $this->assertSame($stateAdditionalResult, $telegramUser->state_additional);
    }
}
