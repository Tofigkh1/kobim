<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;

class VoenApiController extends Controller
{
    public static function getByVOEN($voen)
    {
        if ($voen === '1004286921') {
            return [
                "name" => "\"BAKI UNİVERSAL TƏDRİS MƏRKƏZİ\" MƏHDUD MƏSULİYYƏTLİ CƏMİYYƏTİ",
                "contact_number" => [
                    "0552122280",
                    "nicatk@mail.ru",
                    "0552000197"
                ],
                "address" => "AZ1117, BAKI ŞƏHƏRİ BİNƏQƏDİ RAYONU, NATƏVAN (BİLƏCƏRİ QƏS.), ev 27, mənzil 16",
                "activity" => [
                    [
                        "activityCode" => "85312",
                        "activityName" => "HAZIRLIQ KURSLARININ FƏALİYYƏTİ"
                    ]
                ],
                "labor_count" => 5,
                "size_type" => 0,
                "legal_status" => 2,
                "meyar" => "Mikro"
            ];
        }
        else{
            return [
                "statusCode"=>417
            ];
        }
    }
}
