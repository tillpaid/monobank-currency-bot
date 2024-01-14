<?php

declare(strict_types=1);

namespace App\Telegram\Processes\ProcessState;

use App\Models\TelegramUser;

class ProcessTelegramDefaultState extends AbstractProcessTelegramState
{
    public function getState(): ?string
    {
        return null;
    }

    public function process(TelegramUser $telegramUser, string $messageText): string
    {
        switch ($messageText) {
            case __('telegram_buttons.buy'):
                $this->telegramUserService->updateState($telegramUser, TelegramUser::STATE_BUY);
                $responseMessage = __('telegram.chooseCurrencyBuy');

                break;

            case __('telegram_buttons.sell'):
                $this->telegramUserService->updateState($telegramUser, TelegramUser::STATE_SELL);
                $responseMessage = __('telegram.chooseCurrencySell');

                break;

            case __('telegram_buttons.balance'):
                $responseMessage = $this->telegramBotService->buildUserBalanceMessage($telegramUser->getId());

                break;

            case __('telegram_buttons.report'):
                $responseMessage = $this->telegramBotService->buildUserReport($telegramUser->getId());

                break;

            case __('telegram_buttons.statisticsCurrency'):
                $this->telegramUserService->updateState($telegramUser, TelegramUser::STATE_STATISTICS_CURRENCY);
                $responseMessage = __('telegram.chooseCurrency');

                break;

            default:
                $responseMessage = __('telegram.occurredError');
        }

        return $responseMessage;
    }
}
