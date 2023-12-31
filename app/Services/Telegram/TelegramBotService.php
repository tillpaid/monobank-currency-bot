<?php

declare(strict_types=1);

namespace App\Services\Telegram;

use App\Models\CurrencyRate;
use App\Repositories\CurrencyAccountRepository;
use App\Repositories\CurrencyRateRepository;
use App\Repositories\TelegramUserRepository;
use App\Repositories\TelegramUserSendRateRepository;
use App\Services\Models\TelegramUserSendRateService;
use App\Telegram\MakeTelegramKeyboard;
use Longman\TelegramBot\Exception\TelegramException;
use Longman\TelegramBot\Request;
use Longman\TelegramBot\Telegram;

class TelegramBotService
{
    private ?Telegram $bot;

    public function __construct(
        private TelegramUserRepository $telegramUserRepository,
        private MakeTelegramKeyboard $makeTelegramKeyboard,
        private CurrencyAccountRepository $currencyAccountRepository,
        private CurrencyRateRepository $currencyRateRepository,
        private TelegramUserSendRateService $telegramUserSendRateService,
        private TelegramUserSendRateRepository $telegramUserSendRateRepository,
    ) {
        $this->bot = null;
    }

    /**
     * @throws TelegramException
     */
    public function sendMessage(string $chatId, string $message): void
    {
        $user = $this->telegramUserRepository->getByChatId($chatId);
        $keyboard = $this->makeTelegramKeyboard->getKeyboard($user->getState() ?? null);

        $sendData = [
            'chat_id' => $chatId,
            'text' => $message,
            'parse_mode' => 'markdown',
            'reply_markup' => [
                'remove_keyboard' => true,
                'resize_keyboard' => true,
                'keyboard' => $keyboard,
            ],
        ];

        // Need for Request sendMessage code
        $this->getBot();
        Request::sendMessage($sendData);
    }

    /**
     * @throws TelegramException
     */
    public function getBot(): Telegram
    {
        if (null === $this->bot) {
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
        $userBalanceSum = $this->currencyAccountRepository->getUserBalanceSum($userId);

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

                $balance .= "{$currencyUpper}: {$currencyValueFormatted} ({$uahValueFormatted}₴ | {$avgValueFormatted}₴)\n";
            }
        }

        $uahSum = $this->format($uahSum, 5);

        return __('telegram.userBalanceSum', ['balance' => $balance, 'uahSum' => $uahSum]);
    }

    public function buildUserReport(int $userId): string
    {
        $currencies = config('monobank.currencies');
        $userBalanceSum = $this->currencyAccountRepository->getUserBalanceSum($userId);

        $rateChange = [];
        $accountChange = [];
        $totalSum = [
            'value' => 0,
            'newValue' => 0,
        ];

        foreach ($currencies as $currencyName) {
            if ($lastCurrencyRates = $this->currencyRateRepository->getLastTwoCurrencyRates($currencyName)) {
                [$rateNew, $rateOld] = $lastCurrencyRates;

                $rateChange[] = $this->getRateChange($rateOld, $rateNew, $userId);

                if (array_key_exists($currencyName, $userBalanceSum)) {
                    $accountChange[] = $this->getAccountChange($userBalanceSum[$currencyName], $currencyName, $rateNew->getBuy());
                }

                $this->getAccountSum($userBalanceSum, $rateNew, $totalSum);
            }
        }

        $rateChange = implode("\n", $rateChange);
        $accountChange = implode("\n", $accountChange);

        $totalDiff = $this->getCurrencyDiff($totalSum['value'], $totalSum['newValue']);
        $percentProfit = 0;

        if (($totalSum['value'] / 100) !== 0) {
            $percentProfit = $this->format(($totalSum['newValue'] - $totalSum['value']) / ($totalSum['value'] / 100));
        }

        $sumMessage = __('telegram.userBalanceSumTotal', [
            'uah' => $this->format($totalSum['value']),
            'newUah' => $this->format($totalSum['newValue']),
            'diff' => $totalDiff,
            'percentProfit' => $percentProfit,
        ]);

        // Generate output message
        $reportMessage = __('telegram.userReport');
        if ($rateChange) {
            $reportMessage .= __('telegram.userReportRate', compact('rateChange'));
        }
        if ($accountChange) {
            $reportMessage .= __('telegram.userReportAccount', compact('accountChange'));
        }
        if ($totalSum['value']) {
            $reportMessage .= $sumMessage;
        }

        return $reportMessage;
    }

    public function format(float $number, int $decimals = 2, bool $trim = true): string
    {
        // TODO: Avoid cast to float here
        $output = number_format($number, $decimals, '.', ' ');

        if ($trim) {
            $output = rtrim($output, '0');
            $output = rtrim($output, '.');
        }

        return $output;
    }

    private function getRateChange(CurrencyRate $rateOld, CurrencyRate $rateNew, int $userId): string
    {
        $currencyName = mb_strtoupper($rateNew->getCurrency());

        $buy = $this->format($rateNew->getBuy(), 5);
        $sell = $this->format($rateNew->getSell(), 5);
        $buyDiff = $this->getCurrencyDiff($rateOld->getBuy(), $rateNew->getBuy(), 5);
        $sellDiff = $this->getCurrencyDiff($rateOld->getSell(), $rateNew->getSell(), 5);

        $rateBeenSent = $this->telegramUserSendRateRepository->findByTelegramUserAndCurrencyRate($userId, $rateNew->getId());

        if ($rateBeenSent) {
            $output = "{$currencyName}: {$buy} / {$sell}";
        } else {
            $output = "{$currencyName}: {$buy} / {$sell} (*{$buyDiff} / {$sellDiff}*)";
            $this->telegramUserSendRateService->updateSendRate($userId, $rateNew->getId(), $currencyName);
        }

        return $output;
    }

    private function getAccountChange(array $userBalanceSum, string $currencyName, float $buyRate): string
    {
        $currencyNameUpper = mb_strtoupper($currencyName);

        $uah = $userBalanceSum['uah_value'];
        $currency = $userBalanceSum['currency_value'];
        $newUah = $currency * $buyRate;
        $diff = $this->getCurrencyDiff($uah, $newUah);
        $avg = $uah / $currency;
        $percentProfit = 0;

        if (($uah / 100) !== 0) {
            $percentProfit = $this->format(($newUah - $uah) / ($uah / 100));
        }

        return __('telegram.userBalanceSumItem', [
            'currencyNameUpper' => $currencyNameUpper,
            'currency' => $this->format($currency),
            'uah' => $this->format($uah),
            'avg' => $this->format($avg),
            'newUah' => $this->format($newUah),
            'diff' => $diff,
            'percentProfit' => $percentProfit,
        ]);
    }

    private function getAccountSum(array $userBalanceSum, CurrencyRate $rateNew, array &$totalSum): void
    {
        if (array_key_exists($rateNew->getCurrency(), $userBalanceSum)) {
            $balance = $userBalanceSum[$rateNew->getCurrency()];

            $totalSum['value'] += $balance['uah_value'];
            $totalSum['newValue'] += $balance['currency_value'] * $rateNew->getBuy();
        }
    }

    private function getCurrencyDiff(float $old, float $new, int $decimals = 2): string
    {
        $diff = $new - $old;
        $formattedDiff = $this->format($diff, $decimals);

        return $diff >= 0 ? "+{$formattedDiff}" : "{$formattedDiff}";
    }
}
