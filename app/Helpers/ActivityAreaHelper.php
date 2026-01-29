<?php
namespace App\Helpers;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;

class ActivityAreaHelper
{
    protected static $cacheKey = 'activity_areas';

    public static function getActivityAreaNameByCode($code)
    {
        $areas = self::fetchActivityAreasFromApi();

        return $areas[$code] ?? 'Unknown';
    }

    protected static function fetchActivityAreasFromApi()
    {


        try {
            $response = Http::timeout(10)->get('http://10.3.30.16/api/activitiyAreasList');

            if ($response->successful()) {
                $data = $response->json();
                $areas = [];

                foreach ($data as $area) {
                    $areas[$area['activityCode']] = $area['activityName'];
                }

                Cache::put(self::$cacheKey, $areas, now()->addHours(24));

                return $areas;
            }
        } catch (\Exception $e) {
        }

        return [];
    }

    public static function getActivityAreaCodeByName($name)
    {
        $areas = self::fetchActivityAreasFromApi();

        foreach ($areas as $code => $areaName) {
            if (stripos($areaName, $name) !== false) {
                return $code;
            }
        }

        return null;
    }
}
