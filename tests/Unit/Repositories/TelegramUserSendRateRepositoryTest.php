<?php

declare(strict_types=1);

namespace Tests\Unit\Repositories;

use App\Repositories\TelegramUserSendRateRepository;
use Illuminate\Contracts\Container\BindingResolutionException;
use Tests\TestCase;

class TelegramUserSendRateRepositoryTest extends TestCase
{
    private TelegramUserSendRateRepository $telegramUserSendRateRepository;

    /**
     * @throws BindingResolutionException
     */
    protected function setUp(): void
    {
        parent::setUp();

        $this->telegramUserSendRateRepository = $this->app->make(TelegramUserSendRateRepository::class);
    }

    /** @dataProvider rowExistsDataProvider */
    public function testRowExists(bool $expectedResult, int $telegramUserId, int $currencyRateId, bool $rowsExist): void
    {
        if ($rowsExist) {
            $telegramUser = $this->fixturesHelper->createTelegramUser();
            $currencyRate = $this->fixturesHelper->createCurrencyRate();

            $this->fixturesHelper->createTelegramUserSendRate($telegramUser->id, $currencyRate->id);
        }

        $result = $this->telegramUserSendRateRepository->rowExists($telegramUserId, $currencyRateId);
        $this->assertSame($expectedResult, $result);
    }

    public function rowExistsDataProvider(): array
    {
        return [
            'Row exists' => [
                'expectedResult' => true,
                'telegramUserId' => 1,
                'currencyRateId' => 1,
                'rowsExist' => true,
            ],
            'Row does not exist' => [
                'expectedResult' => false,
                'telegramUserId' => 1,
                'currencyRateId' => 1,
                'rowsExist' => false,
            ],
            'Another row exist' => [
                'expectedResult' => false,
                'telegramUserId' => 1,
                'currencyRateId' => 2,
                'rowsExist' => true,
            ],
        ];
    }

    public function testGetSendRateExists(): void
    {
        $telegramUser = $this->fixturesHelper->createTelegramUser();
        $currencyRate = $this->fixturesHelper->createCurrencyRate();
        $sendRate = $this->fixturesHelper->createTelegramUserSendRate($telegramUser->id, $currencyRate->id);

        $result = $this->telegramUserSendRateRepository->getSendRate($telegramUser->id, $currencyRate->currency);
        $this->assertSame($sendRate->id, $result->id);
    }

    public function testGetSendRateNotExists(): void
    {
        $telegramUser = $this->fixturesHelper->createTelegramUser();
        $currencyRate = $this->fixturesHelper->createCurrencyRate();

        $result = $this->telegramUserSendRateRepository->getSendRate($telegramUser->id, $currencyRate->currency);
        $this->assertNull($result);
    }
}
