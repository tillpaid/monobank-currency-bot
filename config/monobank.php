<?php

return [
    'currencies' => ['usd', 'eur'],
    'uahCode' => 980,
    'currencyCodes' => [
        840 => 'usd',
        978 => 'eur',
    ],
    'monobank_currency_url' => env('MONOBANK_CURRENCY_URL', 'https://api.monobank.ua/bank/currency'),
];
