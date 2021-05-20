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
     * @var ProcessTelegramBuyState
     */
    private $processTelegramBuyState;
    /**
     * @var ProcessTelegramBuySumState
     */
    private $processTelegramBuySumState;
    /**
     * @var ProcessTelegramBuyRateState
     */
    private $processTelegramBuyRateState;
    /**
     * @var ProcessTelegramBuyRateOwnState
     */
    private $processTelegramBuyRateOwnState;
    /**
     * @var ProcessTelegramSellState
     */
    private $processTelegramSellState;
    /**
     * @var ProcessTelegramSellSumState
     */
    private $processTelegramSellSumState;
    /**
     * @var ProcessTelegramSellConfirmState
     */
    private $processTelegramSellConfirmState;

    /**
     * @var ProcessTelegramStatisticsCurrencyState
     */
    private $processTelegramStatisticsCurrencyState;

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
        $this->processTelegramBuyState = $processTelegramBuyState;
        $this->processTelegramBuySumState = $processTelegramBuySumState;
        $this->processTelegramBuyRateState = $processTelegramBuyRateState;
        $this->processTelegramBuyRateOwnState = $processTelegramBuyRateOwnState;
        $this->processTelegramSellState = $processTelegramSellState;
        $this->processTelegramSellSumState = $processTelegramSellSumState;
        $this->processTelegramSellConfirmState = $processTelegramSellConfirmState;
        $this->processTelegramStatisticsCurrencyState = $processTelegramStatisticsCurrencyState;
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
        switch ($user->state) {
            case config('states.buy'):
                $processor = $this->processTelegramBuyState;
                break;
            case config('states.buy-sum'):
                $processor = $this->processTelegramBuySumState;
                break;
            case config('states.buy-rate'):
                $processor = $this->processTelegramBuyRateState;
                break;
            case config('states.buy-rate-own'):
                $processor = $this->processTelegramBuyRateOwnState;
                break;
            case config('states.sell'):
                $processor = $this->processTelegramSellState;
                break;
            case config('states.sell-sum'):
                $processor = $this->processTelegramSellSumState;
                break;
            case config('states.sell-confirm'):
                $processor = $this->processTelegramSellConfirmState;
                break;
            case config('states.statistics-currency'):
                $processor = $this->processTelegramStatisticsCurrencyState;
                break;
            default:
                $processor = $this->processTelegramDefaultState;
        }

        return $processor;
    }
}
