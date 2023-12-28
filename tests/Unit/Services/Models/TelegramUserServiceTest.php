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

    public function getCountByChatId(string $chatId): int
    {
        return TelegramUser::query()->where('chat_id', $chatId)->count();
    }

    public function testUpdateState(): void
    {
        $state = 'value';
        $telegramUser = TelegramUser::factory()->create();

        $this->assertNull($telegramUser->getState());
        $this->telegramUserService->updateState($telegramUser, $state, []);
        $this->assertSame($state, $telegramUser->getState());
    }

    public function testUpdateStateWithStateAdditional(): void
    {
        $state = 'value';
        $stateAdditional = ['key' => 'value'];
        $telegramUser = TelegramUser::factory()->create();

        $this->assertNull($telegramUser->getState());
        $this->assertNull($telegramUser->getStateAdditional());
        $this->telegramUserService->updateState($telegramUser, $state, $stateAdditional);
        $this->assertSame($state, $telegramUser->getState());
        $this->assertSame($stateAdditional, $telegramUser->getStateAdditional());
    }

    public function testUpdateStateAdditional(): void
    {
        $telegramUser = TelegramUser::factory()->create();
        $this->assertNull($telegramUser->getStateAdditional());

        $stateAdditional = ['key' => 'value'];
        $this->telegramUserService->updateStateAdditional($telegramUser, $stateAdditional);
        $this->assertSame($stateAdditional, $telegramUser->getStateAdditional());

        $stateAdditionalNew = ['key1' => 'value1'];
        $stateAdditionalResult = array_merge($stateAdditional, $stateAdditionalNew);

        $this->telegramUserService->updateStateAdditional($telegramUser, $stateAdditionalNew);
        $this->assertSame($stateAdditionalResult, $telegramUser->getStateAdditional());
    }
}
