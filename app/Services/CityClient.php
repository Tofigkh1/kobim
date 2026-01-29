<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class CityClient {
    
    public function getCity()
    {
        try {
            $response = Http::get('http://10.3.30.12/api/cities');

            if ($response->successful() && isset($response->json()['data'])) {
                return $response->json();
            }
        } catch (\Exception $e) {
         
        }

        
        return [
            'data' => [
                ['id' => null, 'name' => 'Server xətası !']
            ]
        ];
    }

    public function getCityOptions()
    {
        $cities = $this->getCity();
        
        return collect($cities['data'])->pluck('name', 'id')->toArray();
    }
}

