<?php

namespace App\Services;

use App\Services\Interfaces\MonobankServiceInterface;
use Illuminate\Support\Facades\Http;

class MonobankService implements MonobankServiceInterface
{
    public function getCurrency(): array
    {
        $output = [];
        $response = Http::get(config('app.monobank_currency_url'));

        if ($response->status() == 200 && $response->body()) {
            $output = json_decode($response->body(), true);
        }

        return $output;
    }
}
