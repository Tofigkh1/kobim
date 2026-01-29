<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class IncludeBusiness
{
    // Varsayılan veriler
    protected $defaultData = [
        [
            "id" => 0,
            "name" => "--"
        ],
    ];

    public function getBusinessStages()
    {
        try {
            $response = Http::timeout(10)->get('http://10.3.30.12/api/business-stages');

            if ($response->ok() && !empty($response->json())) {
                return $response->json();
            }
        } catch (\Exception $e) {
            // Hata durumunda varsayılan verileri döndür
        }

        return $this->defaultData;
    }

    public function getBusinessStageOptions()
    {
        $stages = $this->getBusinessStages();

        return collect($stages)->pluck('name', 'id')->toArray();
    }
}
