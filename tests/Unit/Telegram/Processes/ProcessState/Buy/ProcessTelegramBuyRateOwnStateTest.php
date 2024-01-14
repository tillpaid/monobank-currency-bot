<?php

declare(strict_types=1);

namespace Tests\Unit\Telegram\Processes\ProcessState\Buy;

use App\Models\TelegramUser;
use App\Telegram\Processes\ProcessState\Buy\ProcessTelegramBuyRateOwnState;
use Exception;
use Tests\TestCase;

class ProcessTelegramBuyRateOwnStateTest extends TestCase
{
    private ProcessTelegramBuyRateOwnState $processTelegramBuyRateOwnState;
    private TelegramUser $telegramUser;

    /**
     * @throws Exception
     */
    protected function setUp(): void
    {
        parent::setUp();

        $this->processTelegramBuyRateOwnState = $this->app->make(ProcessTelegramBuyRateOwnState::class);

        $this->telegramUser = $this->fixturesHelper->createTelegramUser(state: TelegramUser::STATE_BUY_RATE_OWN);
        $this->fixturesHelper->createCurrencyRate('usd');
    }

    public function testProcessOwnRate(): void
    {
        $messageText = '2.34';
        $response = $this->processTelegramBuyRateOwnState->process($this->telegramUser, $messageText);

        /** @var TelegramUser $telegramUser */
        $telegramUser = TelegramUser::query()->find($this->telegramUser->id);
        $this->assertSame(TelegramUser::STATE_BUY_RATE, $telegramUser->getState());
        $this->assertSame(
            2.34,
            $telegramUser->getStateAdditionalFloatValue(TelegramUser::STATE_ADDITIONAL_BUY_CURRENCY_RATE)
        );
    }

    public function testProcessOwnRateLessThanZero(): void
    {
        $messageText = '-1';
        $response = $this->processTelegramBuyRateOwnState->process($this->telegramUser, $messageText);

        /** @var TelegramUser $telegramUser */
        $telegramUser = TelegramUser::query()->find($this->telegramUser->id);
        $this->assertSame(TelegramUser::STATE_BUY_RATE_OWN, $telegramUser->getState());

        $this->assertSame(__('telegram.numberMustBeGreaterThanZero'), $response);
    }

    public function testProcessOwnRateIsZero(): void
    {
        $messageText = '0';
        $response = $this->processTelegramBuyRateOwnState->process($this->telegramUser, $messageText);

        /** @var TelegramUser $telegramUser */
        $telegramUser = TelegramUser::query()->find($this->telegramUser->id);
        $this->assertSame(TelegramUser::STATE_BUY_RATE_OWN, $telegramUser->getState());

        $this->assertSame(__('telegram.numberMustBeGreaterThanZero'), $response);
    }

    public function testProcessBackButton(): void
    {
        $this->processTelegramBuyRateOwnState->process($this->telegramUser, __('telegram_buttons.back'));

        /** @var TelegramUser $telegramUser */
        $telegramUser = TelegramUser::query()->find($this->telegramUser->id);
        $this->assertSame(TelegramUser::STATE_BUY_RATE, $telegramUser->getState());
    }

    public function testProcessBackHomeButton(): void
    {
        $this->processTelegramBuyRateOwnState->process($this->telegramUser, __('telegram_buttons.backHome'));

        /** @var TelegramUser $telegramUser */
        $telegramUser = TelegramUser::query()->find($this->telegramUser->id);
        $this->assertSame(TelegramUser::STATE_DEFAULT, $telegramUser->getState());
    }

    public function testOccurredError(): void
    {
        $messageText = 'Some text';
        $response = $this->processTelegramBuyRateOwnState->process($this->telegramUser, $messageText);

        /** @var TelegramUser $telegramUser */
        $telegramUser = TelegramUser::query()->find($this->telegramUser->id);
        $this->assertSame(TelegramUser::STATE_BUY_RATE_OWN, $telegramUser->getState());

        $this->assertSame(__('telegram.occurredError'), $response);
    }
}
