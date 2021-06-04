<?php

namespace App\Telegram\Processes;

use App\Telegram\Processes\ProcessState\Buy\ProcessTelegramBuyRateOwnState;
use App\Telegram\Processes\ProcessState\Buy\ProcessTelegramBuyRateState;
use App\Telegram\Processes\ProcessState\Buy\ProcessTelegramBuyState;
use App\Telegram\Processes\ProcessState\Buy\ProcessTelegramBuySumState;
use App\Telegram\Processes\ProcessState\Interfaces\ProcessTelegramStateInterface;
use App\Telegram\Processes\ProcessState\ProcessTelegramDefaultState;
use App\Telegram\Processes\ProcessState\Sell\ProcessTelegramSellConfirmState;
use App\Telegram\Processes\ProcessState\Sell\ProcessTelegramSellState;
use App\Telegram\Processes\ProcessState\Sell\ProcessTelegramSellSumState;
use App\Telegram\Processes\ProcessState\Statistics\ProcessTelegramStatisticsCurrencyState;
use Illuminate\Database\Eloquent\Model;

/**
 * Class ProcessTelegramState
 * @package App\Telegram\Processes
 */
class ProcessTelegramState
{
    /**
     * @var ProcessTelegramDefaultState
     */
    private $processTelegramDefaultState;
    /**
     * @var array
     */
    private $processors;

    /**
     * ProcessTelegramState constructor.
     * @param ProcessTelegramDefaultState $processTelegramDefaultState
     * @param ProcessTelegramBuyState $processTelegramBuyState
     * @param ProcessTelegramBuySumState $processTelegramBuySumState
     * @param ProcessTelegramBuyRateState $processTelegramBuyRateState
     * @param ProcessTelegramBuyRateOwnState $processTelegramBuyRateOwnState
     * @param ProcessTelegramSellState $processTelegramSellState
     * @param ProcessTelegramSellSumState $processTelegramSellSumState
     * @param ProcessTelegramSellConfirmState $processTelegramSellConfirmState
     * @param ProcessTelegramStatisticsCurrencyState $processTelegramStatisticsCurrencyState
     * @return void
     */
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
    )
    {
        $this->processTelegramDefaultState = $processTelegramDefaultState;
        $this->processors = [
            config('states.buy')                 => $processTelegramBuyState,
            config('states.buy-sum')             => $processTelegramBuySumState,
            config('states.buy-rate')            => $processTelegramBuyRateState,
            config('states.buy-rate-own')        => $processTelegramBuyRateOwnState,
            config('states.sell')                => $processTelegramSellState,
            config('states.sell-sum')            => $processTelegramSellSumState,
            config('states.sell-confirm')        => $processTelegramSellConfirmState,
            config('states.statistics-currency') => $processTelegramStatisticsCurrencyState,
        ];
    }

    /**
     * @param Model $user
     * @param string $messageText
     * @return string
     */
    public function process(Model $user, string $messageText): string
    {
        $processor = $this->getProcessor($user);
        return $processor->process($user, $messageText);
    }

    /**
     * @param Model $user
     * @return ProcessTelegramStateInterface
     */
    private function getProcessor(Model $user): ProcessTelegramStateInterface
    {
        if ($user->state && array_key_exists($user->state, $this->processors)) {
            $output = $this->processors[$user->state];
        } else {
            $output = $this->processTelegramDefaultState;
        }

        return $output;
    }
}
