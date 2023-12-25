<?php

declare(strict_types=1);

namespace App\Telegram\Processes;

use App\Models\TelegramUser;
use App\Telegram\Processes\ProcessState\AbstractProcessTelegramState;
use App\Telegram\Processes\ProcessState\Buy\ProcessTelegramBuyRateOwnState;
use App\Telegram\Processes\ProcessState\Buy\ProcessTelegramBuyRateState;
use App\Telegram\Processes\ProcessState\Buy\ProcessTelegramBuyState;
use App\Telegram\Processes\ProcessState\Buy\ProcessTelegramBuySumState;
use App\Telegram\Processes\ProcessState\ProcessTelegramDefaultState;
use App\Telegram\Processes\ProcessState\Sell\ProcessTelegramSellConfirmState;
use App\Telegram\Processes\ProcessState\Sell\ProcessTelegramSellState;
use App\Telegram\Processes\ProcessState\Sell\ProcessTelegramSellSumState;
use App\Telegram\Processes\ProcessState\Statistics\ProcessTelegramStatisticsCurrencyState;

class ProcessTelegramState
{
    private ProcessTelegramDefaultState $processTelegramDefaultState;
    /** @var AbstractProcessTelegramState[] */
    private array $processors;

    public function __construct(
        ProcessTelegramDefaultState $processTelegramDefaultState,
        ProcessTelegramBuyState $processTelegramBuyState,
        ProcessTelegramBuySumState $processTelegramBuySumState,
        ProcessTelegramBuyRateState $processTelegramBuyRateState,
        ProcessTelegramBuyRateOwnState $processTelegramBuyRateOwnState,
        ProcessTelegramSellState $processTelegramSellState,
        ProcessTelegramSellSumState $processTelegramSellSumState,
        ProcessTelegramSellConfirmState $processTelegramSellConfirmState,
        ProcessTelegramStatisticsCurrencyState $processTelegramStatisticsCurrencyState
    ) {
        $this->processTelegramDefaultState = $processTelegramDefaultState;
        $this->processors = [
            config('states.buy') => $processTelegramBuyState,
            config('states.buy-sum') => $processTelegramBuySumState,
            config('states.buy-rate') => $processTelegramBuyRateState,
            config('states.buy-rate-own') => $processTelegramBuyRateOwnState,
            config('states.sell') => $processTelegramSellState,
            config('states.sell-sum') => $processTelegramSellSumState,
            config('states.sell-confirm') => $processTelegramSellConfirmState,
            config('states.statistics-currency') => $processTelegramStatisticsCurrencyState,
        ];
    }

    public function process(TelegramUser $telegramUser, string $messageText): string
    {
        $processor = $this->getProcessor($telegramUser);

        return $processor->process($telegramUser, $messageText);
    }

    private function getProcessor(TelegramUser $telegramUser): AbstractProcessTelegramState
    {
        if ($telegramUser->state && array_key_exists($telegramUser->state, $this->processors)) {
            $output = $this->processors[$telegramUser->state];
        } else {
            $output = $this->processTelegramDefaultState;
        }

        return $output;
    }
}
