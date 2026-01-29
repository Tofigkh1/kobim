<?php

namespace App\Services;

use App\Interfaces\UserDetailsServiceInterface;
use Illuminate\Support\Facades\Http;

class ApiClient implements UserDetailsServiceInterface
{
    protected string $baseUrl;

    public function __construct()
    {
        $this->baseUrl = 'http://10.3.30.16/reystr';
    }

    public function getUserDetailsByVoen(string $voen): array
    {
        try {
            $response = Http::get("{$this->baseUrl}/byvoen?voen={$voen}")->json();

            return $response ?: [];
        } catch (\Exception $e) {
            return [];
        }
    }

    public function getUserDetailsByFin(string $fin): array
    {
        try {
            $response = Http::timeout(5)
                ->get("{$this->baseUrl}/bypin?pin={$fin}")
                ->json();

            return $response ?: ["statusCode" => 417];
        } catch (\Exception $e) {
            return [];
        }
    }
}
