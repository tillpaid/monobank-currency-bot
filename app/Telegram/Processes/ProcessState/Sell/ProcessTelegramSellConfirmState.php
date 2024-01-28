<?php

declare(strict_types=1);

namespace App\Telegram\Processes\ProcessState\Sell;

use App\Models\TelegramUser;
use App\Telegram\Processes\ProcessState\AbstractProcessTelegramState;

class ProcessTelegramSellConfirmState extends AbstractProcessTelegramState
{
    public function getState(): ?string
    {
        return TelegramUser::STATE_SELL_CONFIRM;
    }

    public function process(TelegramUser $telegramUser, string $messageText): string
    {
        return match ($messageText) {
            __('telegram_buttons.confirm') => $this->processConfirmAction($telegramUser),
            __('telegram_buttons.back') => $this->processBackButton($telegramUser),
            __('telegram_buttons.backHome') => $this->processBackHomeButton($telegramUser),
            default => __('telegram.occurredError'),
        };
    }

    private function processConfirmAction(TelegramUser $telegramUser): string
    {
        $currency = $this->telegramUserAdditionalStateResolver->getCurrency($telegramUser);
        $currencySum = $this->telegramUserAdditionalStateResolver->getCurrencySum($telegramUser);

        $this->currencyAccountService->sellCurrency($telegramUser->getId(), $currency, $currencySum);
        $this->telegramUserService->updateState($telegramUser, TelegramUser::STATE_DEFAULT);

        $responseMessage = __('telegram.sellSuccessMessage');
        $responseMessage .= __('telegram.delimiter');
        $responseMessage .= $this->telegramBotService->buildUserBalanceMessage($telegramUser->getId());

        return $responseMessage;
    }

    private function processBackButton(TelegramUser $telegramUser): string
    {
        $this->telegramUserService->updateState($telegramUser, TelegramUser::STATE_SELL_SUM);

        $currency = $this->telegramUserAdditionalStateResolver->getCurrency($telegramUser);
        $currencySum = $this->telegramUserAdditionalStateResolver->getCurrencySumAllFormatted($telegramUser);

        return __('telegram.sellSum', ['currencySum' => $currencySum, 'currency' => mb_strtoupper($currency)]);
    }
}
