<?php

declare(strict_types=1);

namespace Tests\Unit\Repositories;

use App\Repositories\TelegramUserRepository;
use Illuminate\Contracts\Container\BindingResolutionException;
use Tests\TestCase;

class TelegramUserRepositoryTest extends TestCase
{
    private TelegramUserRepository $telegramUserRepository;

    /**
     * @throws BindingResolutionException
     */
    protected function setUp(): void
    {
        parent::setUp();

        $this->telegramUserRepository = $this->app->make(TelegramUserRepository::class);
    }

    public function testGetByChatIdExists(): void
    {
        $chatId = '123456789';

        $telegramUser = $this->fixturesHelper->createTelegramUser($chatId);
        $result = $this->telegramUserRepository->getByChatId($chatId);

        $this->assertSame($telegramUser->id, $result->id);
    }

    public function testGetByChatIdNotExists(): void
    {
        $result = $this->telegramUserRepository->getByChatId('123456789');
        $this->assertNull($result);
    }

    public function testCreateIfNotExistsExists(): void
    {
        $chatId = '123456789';

        $this->fixturesHelper->createTelegramUser($chatId);
        $this->telegramUserRepository->createIfNotExists($chatId);

        $result = $this->telegramUserRepository->all();
        $this->assertCount(1, $result);
        $this->assertSame($chatId, $result->first()->chat_id);
    }

    public function testCreateIfNotExistsNotExists(): void
    {
        $chatId = '123456789';

        $this->telegramUserRepository->createIfNotExists($chatId);

        $result = $this->telegramUserRepository->all();
        $this->assertCount(1, $result);
        $this->assertSame($chatId, $result->first()->chat_id);
    }

    public function testAll(): void
    {
        $chatIdOne = '123456789';
        $chatIdTwo = '987654321';
        $chatIdThree = '123123123';

        $this->fixturesHelper->createTelegramUser($chatIdOne);
        $this->fixturesHelper->createTelegramUser($chatIdTwo);
        $this->fixturesHelper->createTelegramUser($chatIdThree);

        $result = $this->telegramUserRepository->all();
        $this->assertCount(3, $result);
        $this->assertSame($chatIdOne, $result[0]->chat_id);
        $this->assertSame($chatIdTwo, $result[1]->chat_id);
        $this->assertSame($chatIdThree, $result[2]->chat_id);
    }
}
