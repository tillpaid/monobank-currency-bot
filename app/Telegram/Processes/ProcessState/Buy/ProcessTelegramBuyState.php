<?php

declare(strict_types=1);

namespace App\Telegram\Processes\ProcessState\Buy;

use App\Models\TelegramUser;
use App\Telegram\Processes\ProcessState\AbstractProcessTelegramState;

class ProcessTelegramBuyState extends AbstractProcessTelegramState
{
    public function getState(): ?string
    {
        return TelegramUser::STATE_BUY;
    }

    public function process(TelegramUser $telegramUser, string $messageText): string
    {
        $messageTextLower = mb_strtolower($messageText);

        if (in_array($messageTextLower, config('monobank.currencies'), true)) {
            return $this->processCurrency($telegramUser, $messageTextLower);
        }

        return match ($messageText) {
            __('telegram_buttons.back') => $this->processBackButton($telegramUser),
            default => __('telegram.currencyNotSupported'),
        };
    }

    private function processCurrency(TelegramUser $telegramUser, string $currency): string
    {
        $this->telegramUserService->updateState(
            $telegramUser,
            TelegramUser::STATE_BUY_SUM,
            [TelegramUser::STATE_ADDITIONAL_BUY_CURRENCY => $currency]
        );

        return __('telegram.buySum');
    }

    private function processBackButton(TelegramUser $telegramUser): string
    {
        $this->telegramUserService->updateState($telegramUser, TelegramUser::STATE_DEFAULT);

        return __('telegram.startMessage');
    }
}
