<?php
namespace App\Helpers;

use Illuminate\Support\Facades\Http;

class CityHelper
{
    public static function getCityNameById($id)
    {
        $cities = self::fetchCitiesFromApi();

        return $cities[$id] ?? 'Unknown';
    }

    protected static function fetchCitiesFromApi()
    {
        $response = Http::get('http://10.3.30.12/api/cities');

        if ($response->successful()) {
            $data = $response->json()['data'];
            $cities = [];

            foreach ($data as $city) {
                $cities[$city['id']] = $city['name'];
            }

            return $cities;
        }

        return [];
    }

    public static function getCityIdByName($name)
    {
        $cities = self::fetchCitiesFromApi();

        foreach ($cities as $id => $cityName) {
            if (stripos($cityName, $name) !== false) {
                return $id;
            }
        }

        return null;
    }
}

