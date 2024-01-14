<?php

declare(strict_types=1);

namespace App\Telegram\Processes\ProcessState\Sell;

use App\Models\TelegramUser;
use App\Telegram\Processes\ProcessState\AbstractProcessTelegramState;

class ProcessTelegramSellSumState extends AbstractProcessTelegramState
{
    public function getState(): ?string
    {
        return TelegramUser::STATE_SELL_SUM;
    }

    public function process(TelegramUser $telegramUser, string $messageText): string
    {
        switch (true) {
            case $messageText === __('telegram_buttons.back'):
                $this->telegramUserService->updateState($telegramUser, TelegramUser::STATE_SELL);
                $responseMessage = __('telegram.chooseCurrencySell');

                break;

            case $messageText === __('telegram_buttons.backHome'):
                $this->telegramUserService->updateState($telegramUser, TelegramUser::STATE_DEFAULT);
                $responseMessage = __('telegram.startMessage');

                break;

            case is_numeric($messageText):
                $messageText = (float) $messageText;

                $currency = $telegramUser->getStateAdditionalValue(TelegramUser::STATE_ADDITIONAL_SELL_CURRENCY) ?? 'usd';
                $currencySumAll = $telegramUser->getStateAdditionalFloatValue(TelegramUser::STATE_ADDITIONAL_SELL_CURRENCY_SUM_ALL) ?? 0;

                if ($messageText > 0) {
                    if ($currencySumAll >= $messageText) {
                        $currencySum = number_format($messageText, 5, '.', ' ');

                        $this->telegramUserService->updateState(
                            $telegramUser,
                            TelegramUser::STATE_SELL_CONFIRM,
                            [TelegramUser::STATE_ADDITIONAL_SELL_CURRENCY_SUM => $messageText]
                        );
                        $responseMessage = __('telegram.sellConfirm', ['sum' => $currencySum, 'currency' => mb_strtoupper($currency)]);
                    } else {
                        $responseMessage = __('telegram.moreThanHave');
                    }
                } else {
                    $responseMessage = __('telegram.numberMustBeGreaterThanZero');
                }

                break;

            default:
                $responseMessage = __('telegram.occurredError');
        }

        return $responseMessage;
    }
}
