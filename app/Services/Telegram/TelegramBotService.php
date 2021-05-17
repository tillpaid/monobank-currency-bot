<?php

namespace App\Services\Telegram;

use App\Services\Interfaces\Models\CurrencyAccountServiceInterface;
use App\Services\Interfaces\Models\CurrencyRateServiceInterface;
use App\Services\Interfaces\Models\TelegramUserServiceInterface;
use App\Services\Interfaces\Telegram\TelegramBotServiceInterface;
use App\Telegram\MakeTelegramKeyboard;
use Illuminate\Database\Eloquent\Model;
use Longman\TelegramBot\Request;
use Longman\TelegramBot\Telegram;

/**
 * Class TelegramBotService
 * @package App\Services\Telegram
 */
class TelegramBotService implements TelegramBotServiceInterface
{
    /**
     * @var Telegram
     */
    private $bot;
    /**
     * @var TelegramUserServiceInterface
     */
    private $telegramUserService;
    /**
     * @var MakeTelegramKeyboard
     */
    private $makeTelegramKeyboard;
    /**
     * @var CurrencyAccountServiceInterface
     */
    private $currencyAccountService;
    /**
     * @var CurrencyRateServiceInterface
     */
    private $currencyRateService;

    /**
     * TelegramBotService constructor.
     * @param TelegramUserServiceInterface $telegramUserService
     * @param MakeTelegramKeyboard $makeTelegramKeyboard
     * @param CurrencyAccountServiceInterface $currencyAccountService
     * @param CurrencyRateServiceInterface $currencyRateService
     */
    public function __construct(
        TelegramUserServiceInterface $telegramUserService,
        MakeTelegramKeyboard $makeTelegramKeyboard,
        CurrencyAccountServiceInterface $currencyAccountService,
        CurrencyRateServiceInterface $currencyRateService
    )
    {
        $this->telegramUserService = $telegramUserService;
        $this->makeTelegramKeyboard = $makeTelegramKeyboard;
        $this->currencyAccountService = $currencyAccountService;
        $this->currencyRateService = $currencyRateService;
    }

    /**
     * @param string $chatId
     * @param string $message
     * @return void
     * @throws \Longman\TelegramBot\Exception\TelegramException
     */
    public function sendMessage(string $chatId, string $message): void
    {
        $user = $this->telegramUserService->getByChatId($chatId);
        $keyboard = $this->makeTelegramKeyboard->getKeyboard($user->state ?? null);

        $sendData = [
            'chat_id'      => $chatId,
            'text'         => $message,
            'parse_mode'   => 'markdown',
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

    /**
     * @return Telegram
     * @throws \Longman\TelegramBot\Exception\TelegramException
     */
    public function getBot(): Telegram
    {
        if (is_null($this->bot)) {
            $botUserName = config('telegram.botUserName');
            $botApiKey = config('telegram.botApiToken');

            $this->bot = new Telegram($botApiKey, $botUserName);
        }

        return $this->bot;
    }

    /**
     * @return string
     */
    public function getMyId(): string
    {
        return config('telegram.myChatId');
    }

    /**
     * @param int $userId
     * @return string
     */
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
                $currencyValue = $this->format($userBalanceSum[$currency]['currency_value'], 5);
                $uahValue = $this->format($userBalanceSum[$currency]['uah_value'], 5);

                $balance .= "{$currencyUpper}: {$currencyValue} ({$uahValue})\n";
            }
        }

        $uahSum = $this->format($uahSum, 5);

        return __('telegram.userBalanceSum', ['balance' => $balance, 'uahSum' => $uahSum]);
    }

    /**
     * @param int $userId
     * @return string
     */
    public function buildUserReport(int $userId): string
    {
        $currencies = config('monobank.currencies');
        $userBalanceSum = $this->currencyAccountService->getUserBalanceSum($userId);

        $rateChange = [];
        $accountChange = [];
        $totalSum = [
            'value'    => 0,
            'newValue' => 0
        ];

        foreach ($currencies as $currencyName) {
            if ($lastCurrencyRates = $this->currencyRateService->getLastTwoCurrencyRates($currencyName)) {
                [$rateNew, $rateOld] = $lastCurrencyRates;

                $rateChange[] = $this->getRateChange($rateOld, $rateNew);

                if (array_key_exists($currencyName, $userBalanceSum)) {
                    $accountChange[] = $this->getAccountChange($userBalanceSum[$currencyName], $currencyName, $rateNew->buy);
                }

                $this->getAccountSum($userBalanceSum, $rateNew, $totalSum);
            }
        }

        $rateChange = join("\n", $rateChange);
        $accountChange = join("\n", $accountChange);

        $totalDiff = $this->getCurrencyDiff($totalSum['value'], $totalSum['newValue']);
        $sum = "{$this->format($totalSum['value'])}₴ | {$this->format($totalSum['newValue'])}₴ (*{$totalDiff}₴*)";

        // Generate output message
        $reportMessage = __('telegram.userReport');
        if ($rateChange) $reportMessage .= __('telegram.userReportRate', compact('rateChange'));
        if ($accountChange) $reportMessage .= __('telegram.userReportAccount', compact('accountChange'));
        if ($totalSum['value']) $reportMessage .= __('telegram.userReportSum', compact('sum'));

        return $reportMessage;
    }

    /**
     * @param Model $rateOld
     * @param Model $rateNew
     * @return string
     */
    private function getRateChange(Model $rateOld, Model $rateNew): string
    {
        $currencyName = mb_strtoupper($rateNew->currency);

        $buy = $this->format($rateNew->buy, 5);
        $sell = $this->format($rateNew->sell, 5);
        $buyDiff = $this->getCurrencyDiff($rateOld->buy, $rateNew->buy, 5);
        $sellDiff = $this->getCurrencyDiff($rateOld->sell, $rateNew->sell, 5);

        return "{$currencyName}: {$buy} / {$sell} (*{$buyDiff} / {$sellDiff}*)";
    }

    /**
     * @param array $userBalanceSum
     * @param string $currencyName
     * @param float $buyRate
     * @return string
     */
    private function getAccountChange(array $userBalanceSum, string $currencyName, float $buyRate): string
    {
        $currencyNameUpper = mb_strtoupper($currencyName);

        $uah = $userBalanceSum['uah_value'];
        $currency = $userBalanceSum['currency_value'];
        $newUah = $currency * $buyRate;
        $diff = $this->getCurrencyDiff($uah, $newUah);

        return "{$currencyNameUpper}: {$this->format($currency)} ({$this->format($uah)}₴) | {$this->format($newUah)}₴ (*{$diff}₴*)";
    }

    /**
     * @param array $userBalanceSum
     * @param Model $rateNew
     * @param array $totalSum
     * @return void
     */
    private function getAccountSum(array $userBalanceSum, Model $rateNew, array &$totalSum): void
    {
        if (array_key_exists($rateNew->currency, $userBalanceSum)) {
            $balance = $userBalanceSum[$rateNew->currency];

            $totalSum['value'] += $balance['uah_value'];
            $totalSum['newValue'] += $balance['currency_value'] * $rateNew->buy;
        }
    }

    /**
     * @param float $old
     * @param float $new
     * @param int $decimals
     * @return string
     */
    private function getCurrencyDiff(float $old, float $new, int $decimals = 2): string
    {
        $diff = $new - $old;
        $formattedDiff = $this->format($diff, $decimals);
        return $diff >= 0 ? "+$formattedDiff" : "$formattedDiff";
    }

    /**
     * @param $number
     * @param int $decimals
     * @return string
     */
    public function format($number, $decimals = 2): string
    {
        $output = number_format($number, $decimals, '.', ' ');
        $output = rtrim($output, '0');
        return rtrim($output, '.');
    }
}
