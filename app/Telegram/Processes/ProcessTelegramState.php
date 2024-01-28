<?php

declare(strict_types=1);

namespace App\Telegram\Processes;

use App\Models\TelegramUser;
use App\Telegram\Processes\ProcessState\Buy\ProcessTelegramBuyRateOwnState;
use App\Telegram\Processes\ProcessState\Buy\ProcessTelegramBuyRateState;
use App\Telegram\Processes\ProcessState\Buy\ProcessTelegramBuyState;
use App\Telegram\Processes\ProcessState\Buy\ProcessTelegramBuySumState;
use App\Telegram\Processes\ProcessState\ProcessTelegramDefaultState;
use App\Telegram\Processes\ProcessState\ProcessTelegramStateInterface;
use App\Telegram\Processes\ProcessState\Sell\ProcessTelegramSellConfirmState;
use App\Telegram\Processes\ProcessState\Sell\ProcessTelegramSellState;
use App\Telegram\Processes\ProcessState\Sell\ProcessTelegramSellSumState;
use App\Telegram\Processes\ProcessState\Statistics\ProcessTelegramStatisticsCurrencyState;

class ProcessTelegramState
{
    private ProcessTelegramDefaultState $processTelegramDefaultState;

    /** @var ProcessTelegramStateInterface[] */
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
            $processTelegramBuyState->getState() => $processTelegramBuyState,
            $processTelegramBuySumState->getState() => $processTelegramBuySumState,
            $processTelegramBuyRateState->getState() => $processTelegramBuyRateState,
            $processTelegramBuyRateOwnState->getState() => $processTelegramBuyRateOwnState,
            $processTelegramSellState->getState() => $processTelegramSellState,
            $processTelegramSellSumState->getState() => $processTelegramSellSumState,
            $processTelegramSellConfirmState->getState() => $processTelegramSellConfirmState,
            $processTelegramStatisticsCurrencyState->getState() => $processTelegramStatisticsCurrencyState,
        ];
    }

    public function process(TelegramUser $telegramUser, string $messageText): string
    {
        $processor = $this->getProcessor($telegramUser);

        return $processor->process($telegramUser, $messageText);
    }

    private function getProcessor(TelegramUser $telegramUser): ProcessTelegramStateInterface
    {
        if (array_key_exists($telegramUser->getState(), $this->processors)) {
            return $this->processors[$telegramUser->getState()];
        }

        return $this->processTelegramDefaultState;
    }
}
