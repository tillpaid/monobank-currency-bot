<?php

declare(strict_types=1);

namespace App\Telegram\Processes\ProcessState\Buy;

use App\Models\TelegramUser;
use App\Telegram\Processes\ProcessState\AbstractProcessTelegramState;

class ProcessTelegramBuyRateOwnState extends AbstractProcessTelegramState
{
    public function getState(): ?string
    {
        return TelegramUser::STATE_BUY_RATE_OWN;
    }

    public function process(TelegramUser $telegramUser, string $messageText): string
    {
        if (is_numeric($messageText)) {
            return $this->processOwnRate($telegramUser, $messageText);
        }

        return match ($messageText) {
            __('telegram_buttons.back') => $this->processBackButton($telegramUser),
            __('telegram_buttons.backHome') => $this->processBackHomeButton($telegramUser),
            default => __('telegram.occurredError'),
        };
    }

    private function processOwnRate(TelegramUser $telegramUser, string $messageText): string
    {
        $rate = (float) $messageText;

        if ($rate <= 0) {
            return __('telegram.numberMustBeGreaterThanZero');
        }

        $this->telegramUserService->updateState($telegramUser, TelegramUser::STATE_BUY_RATE);

        return $this->buildBuyConfirmMessage($telegramUser, $rate);
    }

    private function processBackButton(TelegramUser $telegramUser): string
    {
        $this->telegramUserService->updateState($telegramUser, TelegramUser::STATE_BUY_RATE);

        return $this->buildBuyConfirmMessage($telegramUser);
    }

    private function processBackHomeButton(TelegramUser $telegramUser): string
    {
        $this->telegramUserService->updateState($telegramUser, TelegramUser::STATE_DEFAULT);

        return __('telegram.startMessage');
    }
}
