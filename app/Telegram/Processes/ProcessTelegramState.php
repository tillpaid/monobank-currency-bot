<?php

namespace App\Telegram\Processes;

use App\Telegram\Processes\ProcessState\Interfaces\ProcessTelegramStateInterface;
use App\Telegram\Processes\ProcessState\ProcessTelegramDefaultState;
use Illuminate\Database\Eloquent\Model;

class ProcessTelegramState
{
    public function process(Model $user, string $messageText): string
    {
        $processor = $this->getProcessor($user);
        return $processor->process($user, $messageText);
    }

    private function getProcessor(Model $user): ProcessTelegramStateInterface
    {
        switch ($user->state) {
            default:
                $processor = new ProcessTelegramDefaultState();
        }

        return $processor;
    }
}
