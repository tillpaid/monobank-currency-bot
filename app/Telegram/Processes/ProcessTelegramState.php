<?php

namespace App\Telegram\Processes;

use App\Telegram\Processes\ProcessState\Buy\ProcessTelegramBuyRateOwnState;
use App\Telegram\Processes\ProcessState\Buy\ProcessTelegramBuyRateState;
use App\Telegram\Processes\ProcessState\Buy\ProcessTelegramBuyState;
use App\Telegram\Processes\ProcessState\Buy\ProcessTelegramBuySumState;
use App\Telegram\Processes\ProcessState\Interfaces\ProcessTelegramStateInterface;
use App\Telegram\Processes\ProcessState\ProcessTelegramDefaultState;
use Illuminate\Database\Eloquent\Model;

class ProcessTelegramState
{
    private $processTelegramDefaultState;
    private $processTelegramBuyState;
    private $processTelegramBuySumState;
    private $processTelegramBuyRateState;
    private $processTelegramBuyRateOwnState;

    public function __construct(
        ProcessTelegramDefaultState $processTelegramDefaultState,
        ProcessTelegramBuyState $processTelegramBuyState,
        ProcessTelegramBuySumState $processTelegramBuySumState,
        ProcessTelegramBuyRateState $processTelegramBuyRateState,
        ProcessTelegramBuyRateOwnState $processTelegramBuyRateOwnState
    )
    {
        $this->processTelegramDefaultState = $processTelegramDefaultState;
        $this->processTelegramBuyState = $processTelegramBuyState;
        $this->processTelegramBuySumState = $processTelegramBuySumState;
        $this->processTelegramBuyRateState = $processTelegramBuyRateState;
        $this->processTelegramBuyRateOwnState = $processTelegramBuyRateOwnState;
    }

    public function process(Model $user, string $messageText): string
    {
        $processor = $this->getProcessor($user);
        return $processor->process($user, $messageText);
    }

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
            default:
                $processor = $this->processTelegramDefaultState;
        }

        return $processor;
    }
}
