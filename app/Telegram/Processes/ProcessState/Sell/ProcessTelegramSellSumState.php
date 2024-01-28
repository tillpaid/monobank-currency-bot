<?php

declare(strict_types=1);

namespace App\Telegram\Processes\ProcessState\Sell;

use App\Models\TelegramUser;
use App\Services\Models\TelegramUserService;
use App\Services\TelegramUserAdditionalStateResolver;
use App\Telegram\Processes\ProcessState\ProcessTelegramStateInterface;

readonly class ProcessTelegramSellSumState implements ProcessTelegramStateInterface
{
    public function __construct(
        private TelegramUserAdditionalStateResolver $telegramUserAdditionalStateResolver,
        private TelegramUserService $telegramUserService,
    ) {}

    public function getState(): ?string
    {
        return TelegramUser::STATE_SELL_SUM;
    }

    public function process(TelegramUser $telegramUser, string $messageText): string
    {
        if (is_numeric($messageText)) {
            return $this->processSellAction($telegramUser, $messageText);
        }

        return match ($messageText) {
            __('telegram_buttons.back') => $this->processBackButton($telegramUser),
            __('telegram_buttons.backHome') => $this->processBackHomeButton($telegramUser),
            default => __('telegram.occurredError'),
        };
    }

    private function processSellAction(TelegramUser $telegramUser, string $messageText): string
    {
        $amount = (float) $messageText;

        $currency = $this->telegramUserAdditionalStateResolver->getCurrency($telegramUser);
        $currencySumAll = $this->telegramUserAdditionalStateResolver->getCurrencySumAll($telegramUser);

        if ($amount <= 0) {
            return __('telegram.numberMustBeGreaterThanZero');
        }

        if ($amount > $currencySumAll) {
            return __('telegram.moreThanHave');
        }

        $this->telegramUserService->updateState(
            $telegramUser,
            TelegramUser::STATE_SELL_CONFIRM,
            [TelegramUser::STATE_ADDITIONAL_SELL_CURRENCY_SUM => $amount]
        );

        $amountFormatted = number_format($amount, 5, '.', ' ');

        return __('telegram.sellConfirm', ['sum' => $amountFormatted, 'currency' => mb_strtoupper($currency)]);
    }

    private function processBackButton(TelegramUser $telegramUser): string
    {
        $this->telegramUserService->updateState($telegramUser, TelegramUser::STATE_SELL);

        return __('telegram.chooseCurrencySell');
    }

    private function processBackHomeButton(TelegramUser $telegramUser): string
    {
        $this->telegramUserService->updateState($telegramUser, TelegramUser::STATE_DEFAULT);

        return __('telegram.startMessage');
    }
}
