<?php

namespace App\Helpers;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;

class IncludeBusinessHelper
{

    public static function getBusinessStageNameById($id)
    {
        $stages = self::fetchBusinessStagesFromApi();

        return $stages[$id] ?? 'Unknown';
    }

    protected static function fetchBusinessStagesFromApi()
    {

        try {
            $response = Http::timeout(10)->get('http://10.3.30.12/api/business-stages');

            if ($response->successful()) {
                $data = $response->json();
                $stages = [];

                foreach ($data as $stage) {
                    $stages[$stage['id']] = $stage['name'];
                }


               

                return $stages;
            }
        } catch (\Exception $e) {
            // 
        }

        return [];
    }

    public static function getBusinessStageIdByName($name)
    {
        $stages = self::fetchBusinessStagesFromApi();

        foreach ($stages as $id => $stageName) {
            if (stripos($stageName, $name) !== false) {
                return $id;
            }
        }

        return null;
    }
}
