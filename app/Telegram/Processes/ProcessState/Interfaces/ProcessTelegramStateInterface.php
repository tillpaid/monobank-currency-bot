<?php

namespace App\Telegram\Processes\ProcessState\Interfaces;

use Illuminate\Database\Eloquent\Model;

/**
 * Interface ProcessTelegramStateInterface
 * @package App\Telegram\Processes\ProcessState\Interfaces
 */
interface ProcessTelegramStateInterface
{
    /**
     * @param Model $user
     * @param string $messageText
     * @return string
     */
    public function process(Model $user, string $messageText): string;
}
