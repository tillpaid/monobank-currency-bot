<?php

declare(strict_types=1);

namespace Tests\Feature\Services\Telegram;

use App\Services\Telegram\TelegramBotService;
use App\Services\Telegram\TelegramService;
use Exception;
use Illuminate\Contracts\Container\BindingResolutionException;
use Tests\Mocks\TelegramBotServiceMock;
use Tests\TestCase;

final class TelegramServiceTest extends TestCase
{
    private const FILES_PATH = __DIR__.'/Resources/';
    private const TEST_FILES = [
        '1-buy-success-flow.csv',
        '2-buy-success-flow-custom-rate.csv',
        '3-buy-wrong-values.csv',
        '4-buy-return-buttons.csv',
        '5-sell-success-flow.csv',
        '6-sell-wrong-values.csv',
        '7-sell-return-buttons.csv',
        '8-statistics.csv',
        '9-commands.csv',
    ];

    private TelegramService $telegramService;
    private TelegramBotServiceMock $telegramBotService;

    private string $myChatId;

    /**
     * @throws BindingResolutionException
     */
    protected function setUp(): void
    {
        parent::setUp();

        $this->telegramBotService = $this->app->make(TelegramBotServiceMock::class);
        $this->app->instance(TelegramBotService::class, $this->telegramBotService);

        $this->telegramService = $this->app->make(TelegramService::class);
        $this->myChatId = $this->telegramBotService->getMyId();
    }

    public function testFilesStepsValid(): void
    {
        foreach (self::TEST_FILES as $testFile) {
            $csvFile = fopen(self::FILES_PATH.$testFile, 'r');
            $actualStep = 0;

            while ($step = fgetcsv($csvFile)) {
                $stepNumber = $step[0] ?? '';
                $stepRequest = $step[1] ?? '';
                $stepResponse = $step[2] ?? '';

                ++$actualStep;
                $expectedNumber = sprintf('Step %d', $actualStep);

                $this->assertSame($expectedNumber, $stepNumber, sprintf('Wrong step number %s in file %s', $stepNumber, $testFile));
                $this->assertNotEmpty($stepRequest, sprintf('Empty request in %s in file %s', $stepNumber, $testFile));
                $this->assertNotEmpty($stepResponse, sprintf('Empty response in %s in file %s', $stepNumber, $testFile));
            }

            fclose($csvFile);
        }
    }

    /**
     * @throws Exception
     */
    public function testFirstAttempt(): void
    {
        $this->fixturesHelper->createCurrencyRate('usd', 37.4, 36.35);
        $this->fixturesHelper->createCurrencyRate('usd', 38.5, 37.5);

        $this->fixturesHelper->createCurrencyRate('eur', 41.4, 40.35);
        $this->fixturesHelper->createCurrencyRate('eur', 42.5, 41.5);

        foreach (self::TEST_FILES as $testFile) {
            $csvFilePath = sprintf('%s/Resources/%s', __DIR__, $testFile);
            $csvFile = fopen($csvFilePath, 'r');

            while ($step = fgetcsv($csvFile)) {
                $stepNumber = $step[0] ?? '';
                $stepRequest = $step[1] ?? '';
                $stepResponse = $step[2] ?? '';

                $response = $this->sendMessageAndGetResponse($stepRequest);
                $expectedResponse = $this->resolveExpectedResponseFromStepData($stepResponse);

                $this->assertSame($expectedResponse, $response, sprintf('Wrong response in %s in file %s', $stepNumber, $testFile));
            }

            fclose($csvFile);
        }
    }

    private function resolveExpectedResponseFromStepData(string $message): string
    {
        $lines = explode("\n", $message);

        $spacesCount = 0;
        foreach ($lines as $line) {
            if (str_starts_with($line, ' ')) {
                $spacesCount = strlen($line) - strlen(ltrim($line));

                break;
            }
        }

        $lines = array_map(function ($line) use ($spacesCount) {
            if ('' === trim(substr($line, 0, $spacesCount))) {
                $line = substr($line, $spacesCount);
            }

            return $line;
        }, $lines);

        return implode("\n", $lines);
    }

    /**
     * @throws Exception
     */
    private function sendMessageAndGetResponse(string $message): string
    {
        $data = [
            'message' => [
                'text' => $message,
                'chat' => ['id' => $this->myChatId],
            ],
        ];

        $this->telegramService->processWebhook($data);
        $messages = $this->telegramBotService->getAndResetMyMessages();

        if (1 !== count($messages)) {
            throw new Exception(sprintf('Expected 1 message, got %d', count($messages)));
        }

        return $messages[0];
    }
}
