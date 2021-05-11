<?php

namespace App\Services\Telegram;

use App\Services\Interfaces\Models\CurrencyAccountServiceInterface;
use App\Services\Interfaces\Models\TelegramUserServiceInterface;
use App\Services\Interfaces\Telegram\TelegramBotServiceInterface;
use App\Telegram\MakeTelegramKeyboard;
use Longman\TelegramBot\Request;
use Longman\TelegramBot\Telegram;

class TelegramBotService implements TelegramBotServiceInterface
{
    private $bot;
    private $telegramUserService;
    private $makeTelegramKeyboard;
    private $currencyAccountService;

    public function __construct(
        TelegramUserServiceInterface $telegramUserService,
        MakeTelegramKeyboard $makeTelegramKeyboard,
        CurrencyAccountServiceInterface $currencyAccountService
    )
    {
        $this->telegramUserService = $telegramUserService;
        $this->makeTelegramKeyboard = $makeTelegramKeyboard;
        $this->currencyAccountService = $currencyAccountService;
    }

    public function sendMessage(string $chatId, string $message): void
    {
        $user = $this->telegramUserService->getByChatId($chatId);
        $keyboard = $this->makeTelegramKeyboard->getKeyboard($user->state ?? null);

        $sendData = [
            'chat_id'      => $chatId,
            'text'         => $message,
            'reply_markup' => [
                'remove_keyboard' => true,
                'resize_keyboard' => true,
                'keyboard'        => $keyboard
            ]
        ];

        // Need for Request sendMessage code
        $telegram = $this->getBot();
        Request::sendMessage($sendData);
    }

    public function getBot(): Telegram
    {
        if (is_null($this->bot)) {
            $botUserName = config('telegram.botUserName');
            $botApiKey = config('telegram.botApiToken');

            $this->bot = new Telegram($botApiKey, $botUserName);
        }

        return $this->bot;
    }

    public function getMyId(): string
    {
        return config('telegram.myChatId');
    }

    public function buildUserBalanceMessage(int $userId): string
    {
        $userBalanceSum = $this->currencyAccountService->getUserBalanceSum($userId);

        if (empty($userBalanceSum)) {
            return __('telegram.userBalanceEmpty');
        }

        $currencies = config('monobank.currencies');

        $balance = '';
        $uahSum = 0;

        foreach ($currencies as $currency) {
            if (array_key_exists($currency, $userBalanceSum)) {
                $uahSum += $userBalanceSum[$currency]['uah_value'];

                $currencyUpper = mb_strtoupper($currency);
                $currencyValue = number_format($userBalanceSum[$currency]['currency_value'], 5, '.', ' ');
                $uahValue = number_format($userBalanceSum[$currency]['uah_value'], 5, '.', ' ');

                $balance .= "{$currencyUpper}: {$currencyValue} ({$uahValue})\n";
            }
        }

        $uahSum = number_format($uahSum, 5, '.', ' ');

        return __('telegram.userBalanceSum', ['balance' => $balance, 'uahSum' => $uahSum]);
    }
}
