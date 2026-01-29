<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class ActivityAreaClient
{
    // Varsayılan veriler
    protected $defaultData = [
        [
            "activityName" => "--",
            "activityCode" => "88888"
        ],
    ];

    public function getActivityArea()
    {
        try {
            $response = Http::timeout(10)->get('http://10.3.30.16/api/activitiyAreasList');

            if ($response->ok() && !empty($response->json())) {
                return $response->json();
            }
        } catch (\Exception $e) {
              //
        }

        return $this->defaultData;
    }

    public function getAreaOptions()
    {
        $areas = $this->getActivityArea();
        
        return collect($areas)->pluck('activityName', 'activityCode')->toArray();
    }
}
