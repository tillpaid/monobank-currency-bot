<?php

declare(strict_types=1);

namespace App\Telegram\Processes\ProcessState;

use App\Models\TelegramUser;
use App\Services\Models\TelegramUserService;
use App\Services\Telegram\TelegramBotService;

readonly class ProcessTelegramDefaultState implements ProcessTelegramStateInterface
{
    public function __construct(
        private TelegramUserService $telegramUserService,
        private TelegramBotService $telegramBotService,
    ) {}

    public function getState(): ?string
    {
        return null;
    }

    public function process(TelegramUser $telegramUser, string $messageText): string
    {
        return match ($messageText) {
            __('telegram_buttons.buy') => $this->processButtonBuy($telegramUser),
            __('telegram_buttons.sell') => $this->processButtonSell($telegramUser),
            __('telegram_buttons.balance') => $this->processButtonBalance($telegramUser),
            __('telegram_buttons.report') => $this->processButtonReport($telegramUser),
            __('telegram_buttons.statisticsCurrency') => $this->processButtonStatisticsCurrency($telegramUser),
            default => __('telegram.occurredError'),
        };
    }

    private function processButtonBuy(TelegramUser $telegramUser): string
    {
        $this->telegramUserService->updateState($telegramUser, TelegramUser::STATE_BUY);

        return __('telegram.chooseCurrencyBuy');
    }

    private function processButtonSell(TelegramUser $telegramUser): string
    {
        $this->telegramUserService->updateState($telegramUser, TelegramUser::STATE_SELL);

        return __('telegram.chooseCurrencySell');
    }

    private function processButtonBalance(TelegramUser $telegramUser): string
    {
        return $this->telegramBotService->buildUserBalanceMessage($telegramUser->getId());
    }

    private function processButtonReport(TelegramUser $telegramUser): string
    {
        return $this->telegramBotService->buildUserReport($telegramUser->getId());
    }

    private function processButtonStatisticsCurrency(TelegramUser $telegramUser): string
    {
        $this->telegramUserService->updateState($telegramUser, TelegramUser::STATE_STATISTICS_CURRENCY);

        return __('telegram.chooseCurrency');
    }
}
