<?php

declare(strict_types=1);

namespace App\Telegram\Processes\ProcessState\Sell;

use App\Models\TelegramUser;
use App\Repositories\CurrencyAccountRepository;
use App\Repositories\CurrencyRateRepository;
use App\Services\Models\CurrencyAccountService;
use App\Services\Models\CurrencyRateService;
use App\Services\Models\TelegramUserService;
use App\Services\Telegram\TelegramBotService;
use App\Telegram\Processes\ProcessState\AbstractProcessTelegramState;

class ProcessTelegramSellState extends AbstractProcessTelegramState
{
    public function __construct(
        private readonly CurrencyAccountRepository $currencyAccountRepository,
        TelegramUserService $telegramUserService,
        CurrencyRateService $currencyRateService,
        CurrencyAccountService $currencyAccountService,
        CurrencyRateRepository $currencyRateRepository,
        TelegramBotService $telegramBotService,
    ) {
        parent::__construct(
            $telegramUserService,
            $currencyRateService,
            $currencyAccountService,
            $currencyRateRepository,
            $telegramBotService
        );
    }

    public function getState(): ?string
    {
        return TelegramUser::STATE_SELL;
    }

    public function process(TelegramUser $telegramUser, string $messageText): string
    {
        $messageTextLower = mb_strtolower($messageText);

        switch (true) {
            case in_array($messageTextLower, config('monobank.currencies'), true):
                $currencySum = $this->currencyAccountRepository->getUserCurrencySum($telegramUser->getId(), $messageTextLower);

                if ($currencySum > 0) {
                    $this->telegramUserService->updateState(
                        $telegramUser,
                        TelegramUser::STATE_SELL_SUM,
                        [
                            TelegramUser::STATE_ADDITIONAL_SELL_CURRENCY => $messageTextLower,
                            TelegramUser::STATE_ADDITIONAL_SELL_CURRENCY_SUM_ALL => $currencySum,
                        ]
                    );

                    $currencySum = number_format($currencySum, 5, '.', ' ');
                    $responseMessage = __('telegram.sellSum', ['currencySum' => $currencySum, 'currency' => mb_strtoupper($messageText)]);
                } else {
                    $responseMessage = __('telegram.sellEmptySum');
                }

                break;

            case $messageText === __('telegram_buttons.back'):
                $this->telegramUserService->updateState($telegramUser, TelegramUser::STATE_DEFAULT);
                $responseMessage = __('telegram.startMessage');

                break;

            default:
                $responseMessage = __('telegram.currencyNotSupported');
        }

        return $responseMessage;
    }
}
