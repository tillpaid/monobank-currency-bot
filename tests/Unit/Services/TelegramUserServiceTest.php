<?php

namespace Tests\Unit\Services;

use App\Models\TelegramUser;
use App\Services\Models\TelegramUserService;
use Illuminate\Container\Container;
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

        $this->telegramUserService = Container::getInstance()->make(TelegramUserService::class);
    }

    public function testGetByChatId(): void
    {
        $chatId = (string)rand(1000, 10000);

        $expected = TelegramUser::factory()
            ->create(['chat_id' => $chatId])
            ->toArray();

        $result = $this->telegramUserService->getByChatId($chatId);
        $result = $result ? $result->toArray() : $result;

        $this->assertEquals($expected, $result);
    }

    public function getCountByChatId(string $chatId): int
    {
        return TelegramUser::where('chat_id', $chatId)->count();
    }

    public function testCreateIfNotExists(): void
    {
        $chatId = (string)rand(1000, 10000);

        $this->assertEquals(0, $this->getCountByChatId($chatId));
        $this->telegramUserService->createIfNotExists($chatId);
        $this->assertEquals(1, $this->getCountByChatId($chatId));
        $this->telegramUserService->createIfNotExists($chatId);
        $this->assertEquals(1, $this->getCountByChatId($chatId));
    }

    public function testUpdateState(): void
    {
        $state = 'value';
        $telegramUser = TelegramUser::factory()->create();

        $this->assertEquals(null, $telegramUser->state);
        $this->telegramUserService->updateState($telegramUser, $state, []);
        $this->assertEquals($state, $telegramUser->state);
    }

    public function testUpdateStateAdditional(): void
    {
        $telegramUser = TelegramUser::factory()->create();
        $this->assertEquals(null, $telegramUser->state_additional);

        $stateAdditional = ['key' => 'value'];
        $this->telegramUserService->updateStateAdditional($telegramUser, $stateAdditional);
        $this->assertEquals($stateAdditional, $telegramUser->state_additional);

        $stateAdditionalNew = ['key1' => 'value1'];
        $stateAdditionalResult = array_merge($stateAdditional, $stateAdditionalNew);

        $this->telegramUserService->updateStateAdditional($telegramUser, $stateAdditionalNew);
        $this->assertEquals($stateAdditionalResult, $telegramUser->state_additional);
    }
}
