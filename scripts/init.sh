#!/bin/bash

echo "Monobank update"
docker exec monobank_currency_bot_app php artisan monobank:update

echo "Telegram set webhook"
docker exec monobank_currency_bot_app php artisan telegram:set-webhook

