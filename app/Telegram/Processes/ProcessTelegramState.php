<?php

namespace App\Telegram\Processes;

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

    public function __construct(
        ProcessTelegramDefaultState $processTelegramDefaultState,
        ProcessTelegramBuyState $processTelegramBuyState,
        ProcessTelegramBuySumState $processTelegramBuySumState,
        ProcessTelegramBuyRateState $processTelegramBuyRateState
    )
    {
        $this->processTelegramDefaultState = $processTelegramDefaultState;
        $this->processTelegramBuyState = $processTelegramBuyState;
        $this->processTelegramBuySumState = $processTelegramBuySumState;
        $this->processTelegramBuyRateState = $processTelegramBuyRateState;
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
            default:
                $processor = $this->processTelegramDefaultState;
        }

        return $processor;
    }
}
