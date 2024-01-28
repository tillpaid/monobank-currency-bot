<?php

declare(strict_types=1);

namespace App\Telegram\Processes\ProcessState\Buy;

use App\Models\TelegramUser;
use App\Services\BuyCurrencyService;
use App\Services\Models\TelegramUserService;
use App\Telegram\Processes\ProcessState\ProcessTelegramStateInterface;

readonly class ProcessTelegramBuySumState implements ProcessTelegramStateInterface
{
    public function __construct(
        private TelegramUserService $telegramUserService,
        private BuyCurrencyService $buyCurrencyService,
    ) {}

    public function getState(): ?string
    {
        return TelegramUser::STATE_BUY_SUM;
    }

    public function process(TelegramUser $telegramUser, string $messageText): string
    {
        if (is_numeric($messageText)) {
            return $this->processBuySum($telegramUser, $messageText);
        }

        return match ($messageText) {
            __('telegram_buttons.back') => $this->processBackButton($telegramUser),
            __('telegram_buttons.backHome') => $this->processBackHomeButton($telegramUser),
            default => __('telegram.occurredError'),
        };
    }

    private function processBuySum(TelegramUser $telegramUser, string $messageText): string
    {
        $sum = (float) $messageText;

        if ($sum <= 0) {
            return __('telegram.numberMustBeGreaterThanZero');
        }

        $this->telegramUserService->updateState(
            $telegramUser,
            TelegramUser::STATE_BUY_RATE,
            [TelegramUser::STATE_ADDITIONAL_BUY_CURRENCY_SUM => $sum]
        );

        return $this->buyCurrencyService->prepareBuyCurrencyAndGetConfirmMessage($telegramUser);
    }

    private function processBackButton(TelegramUser $telegramUser): string
    {
        $this->telegramUserService->updateState($telegramUser, TelegramUser::STATE_BUY);

        return __('telegram.chooseCurrencyBuy');
    }

    private function processBackHomeButton(TelegramUser $telegramUser): string
    {
        $this->telegramUserService->updateState($telegramUser, TelegramUser::STATE_DEFAULT);

        return __('telegram.startMessage');
    }
}
