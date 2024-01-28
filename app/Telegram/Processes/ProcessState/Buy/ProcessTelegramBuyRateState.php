<?php

declare(strict_types=1);

namespace App\Telegram\Processes\ProcessState\Buy;

use App\Models\TelegramUser;
use App\Telegram\Processes\ProcessState\AbstractProcessTelegramState;

class ProcessTelegramBuyRateState extends AbstractProcessTelegramState
{
    public function getState(): ?string
    {
        return TelegramUser::STATE_BUY_RATE;
    }

    public function process(TelegramUser $telegramUser, string $messageText): string
    {
        return match ($messageText) {
            __('telegram_buttons.confirm') => $this->processConfirmAction($telegramUser),
            __('telegram_buttons.editRate') => $this->processEditRateAction($telegramUser),
            __('telegram_buttons.back') => $this->processBackButton($telegramUser),
            __('telegram_buttons.backHome') => $this->processBackHomeButton($telegramUser),
            default => __('telegram.occurredError'),
        };
    }

    private function processConfirmAction(TelegramUser $telegramUser): string
    {
        $this->telegramUserService->updateState($telegramUser, TelegramUser::STATE_DEFAULT);

        $this->currencyAccountService->create(
            $telegramUser->getId(),
            $telegramUser->getStateAdditionalValue(TelegramUser::STATE_ADDITIONAL_BUY_CURRENCY),
            $telegramUser->getStateAdditionalFloatValue(TelegramUser::STATE_ADDITIONAL_BUY_CURRENCY_SUM),
            $telegramUser->getStateAdditionalFloatValue(TelegramUser::STATE_ADDITIONAL_BUY_CURRENCY_RATE)
        );

        $responseMessage = __('telegram.buySuccessMessage');
        $responseMessage .= __('telegram.delimiter');
        $responseMessage .= $this->telegramBotService->buildUserBalanceMessage($telegramUser->getId());

        return $responseMessage;
    }

    private function processEditRateAction(TelegramUser $telegramUser): string
    {
        $this->telegramUserService->updateState($telegramUser, TelegramUser::STATE_BUY_RATE_OWN);

        return __('telegram.changeRateMessage');
    }

    private function processBackButton(TelegramUser $telegramUser): string
    {
        $this->telegramUserService->updateState($telegramUser, TelegramUser::STATE_BUY_SUM);

        return __('telegram.buySum');
    }
}
