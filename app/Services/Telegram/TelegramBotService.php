<?php

namespace App\Services\Telegram;

use App\Services\Interfaces\Models\CurrencyAccountServiceInterface;
use App\Services\Interfaces\Models\CurrencyRateServiceInterface;
use App\Services\Interfaces\Models\TelegramUserSendRateServiceInterface;
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
     * @var TelegramUserSendRateServiceInterface
     */
    private $telegramUserSendRateService;

    /**
     * TelegramBotService constructor.
     * @param TelegramUserServiceInterface $telegramUserService
     * @param MakeTelegramKeyboard $makeTelegramKeyboard
     * @param CurrencyAccountServiceInterface $currencyAccountService
     * @param CurrencyRateServiceInterface $currencyRateService
     * @param TelegramUserSendRateServiceInterface $telegramUserSendRateService
     */
    public function __construct(
        TelegramUserServiceInterface $telegramUserService,
        MakeTelegramKeyboard $makeTelegramKeyboard,
        CurrencyAccountServiceInterface $currencyAccountService,
        CurrencyRateServiceInterface $currencyRateService,
        TelegramUserSendRateServiceInterface $telegramUserSendRateService
    )
    {
        $this->telegramUserService = $telegramUserService;
        $this->makeTelegramKeyboard = $makeTelegramKeyboard;
        $this->currencyAccountService = $currencyAccountService;
        $this->currencyRateService = $currencyRateService;
        $this->telegramUserSendRateService = $telegramUserSendRateService;
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

                $currencyValue = $userBalanceSum[$currency]['currency_value'];
                $uahValue = $userBalanceSum[$currency]['uah_value'];

                $currencyUpper = mb_strtoupper($currency);
                $currencyValueFormatted = $this->format($currencyValue, 5);
                $uahValueFormatted = $this->format($uahValue, 5);
                $avgValueFormatted = $this->format($uahValue / $currencyValue);

                $balance .= "{$currencyUpper}: {$currencyValueFormatted} ({$uahValueFormatted}??? | {$avgValueFormatted}???)\n";
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

                $rateChange[] = $this->getRateChange($rateOld, $rateNew, $userId);

                if (array_key_exists($currencyName, $userBalanceSum)) {
                    $accountChange[] = $this->getAccountChange($userBalanceSum[$currencyName], $currencyName, $rateNew->buy);
                }

                $this->getAccountSum($userBalanceSum, $rateNew, $totalSum);
            }
        }

        $rateChange = join("\n", $rateChange);
        $accountChange = join("\n", $accountChange);

        $totalDiff = $this->getCurrencyDiff($totalSum['value'], $totalSum['newValue']);
        $percentProfit = 0;

        if (($totalSum['value'] / 100) != 0) {
            $percentProfit = $this->format(($totalSum['newValue'] - $totalSum['value']) / ($totalSum['value'] / 100));
        }

        $sumMessage = __('telegram.userBalanceSumTotal', [
            'uah'           => $this->format($totalSum['value']),
            'newUah'        => $this->format($totalSum['newValue']),
            'diff'          => $totalDiff,
            'percentProfit' => $percentProfit,
        ]);

        // Generate output message
        $reportMessage = __('telegram.userReport');
        if ($rateChange) $reportMessage .= __('telegram.userReportRate', compact('rateChange'));
        if ($accountChange) $reportMessage .= __('telegram.userReportAccount', compact('accountChange'));
        if ($totalSum['value']) $reportMessage .= $sumMessage;

        return $reportMessage;
    }

    /**
     * @param Model $rateOld
     * @param Model $rateNew
     * @param int $userId
     * @return string
     */
    private function getRateChange(Model $rateOld, Model $rateNew, int $userId): string
    {
        $currencyName = mb_strtoupper($rateNew->currency);

        $buy = $this->format($rateNew->buy, 5);
        $sell = $this->format($rateNew->sell, 5);
        $buyDiff = $this->getCurrencyDiff($rateOld->buy, $rateNew->buy, 5);
        $sellDiff = $this->getCurrencyDiff($rateOld->sell, $rateNew->sell, 5);

        $rateBeenSent = $this->telegramUserSendRateService->checkIfRateChangeBeenSent($userId, $rateNew->id);

        if ($rateBeenSent) {
            $output = "{$currencyName}: {$buy} / {$sell}";
        } else {
            $output = "{$currencyName}: {$buy} / {$sell} (*{$buyDiff} / {$sellDiff}*)";
            $this->telegramUserSendRateService->updateSendRate($userId, $rateNew->id, $currencyName);
        }

        return $output;
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
        $avg = $uah / $currency;
        $percentProfit = 0;

        if (($uah / 100) != 0) {
            $percentProfit = $this->format(($newUah - $uah) / ($uah / 100));
        }

        return __('telegram.userBalanceSumItem', [
            'currencyNameUpper' => $currencyNameUpper,
            'currency'          => $this->format($currency),
            'uah'               => $this->format($uah),
            'avg'               => $this->format($avg),
            'newUah'            => $this->format($newUah),
            'diff'              => $diff,
            'percentProfit'     => $percentProfit,
        ]);
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
     * @param bool $trim
     * @return string
     */
    public function format($number, $decimals = 2, $trim = true): string
    {
        $output = number_format($number, $decimals, '.', ' ');

        if ($trim) {
            $output = rtrim($output, '0');
            $output = rtrim($output, '.');
        }

        return $output;
    }
}
