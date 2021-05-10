<?php

namespace App\Telegram\Processes\ProcessState;

use Illuminate\Database\Eloquent\Model;

class ProcessTelegramBuyRateState extends AbstractProcessTelegramState
{
	public function process(Model $user, string $messageText): string
	{
        switch ($messageText) {
            case __('telegram_buttons.back'):
                $this->updateUserState($user, config('states.buy-sum'));
                $responseMessage = __('telegram.buySum');

                break;
            case __('telegram_buttons.confirm'):
                $this->updateUserState($user, null);
                $responseMessage = __('telegram.buySuccessMessage');

                break;
            default:
                $responseMessage = __('telegram.occurredError');
        }

        return $responseMessage;
	}
}
