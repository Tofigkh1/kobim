<?php

namespace App\Http\Resources;

use App\Models\SmeInformation;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TrainingResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */


    protected $businessTypes = [
        "1" => "Biznesi planla",
        "2" => "Biznesə başla",
        "3" => "Biznesə işlət",
        "4" => "Biznesi inkşaf etdir",
    ];
    public function toArray($request)
    {
        $smeName = null;
        if ($this->executive) {
            $smeId = $this->executive->sme_id;
            $sme = SmeInformation::find($smeId);
            if ($sme) {
                $smeName = $sme->smeName;
            }
        }

        return [
            'id' => $this->id,
            'name' => $this->name,
            'date' => $this->date,
            'sme' => [
                'id' => $this->executive ? $this->executive->sme_id : null,
                'name' => $smeName ?? 'Təyin olunmayıb',
            ],
            'hour' => $this->hour,
            'duration' => $this->duration,
            'scope' => $this->scope,
            'serviceType' => $this->serviceType,
            'executive' => $this->whenLoaded('executive', function () {
                return [
                    'id' => $this->executive->id,
                    'name' => $this->executive->fullName,
                    "executive_image" => $this->executive->photo ? url(env('APP_URL') . '/storage/' . $this->executive->photo) : null,

                ];
            }, [
                'id' => null,
                'name' => 'Təyin olunmayıb',
            ]),
            'note' => $this->note,
            'link' => $this->link,
            'address' => $this->address,
            'includesBusiness' => $this->transformBusinessTypes($this->includesBusiness),
            'certificate' => $this->certificate,
            'haveSkills' => $this->haveSkills,
            'status' => $this->status,
            'user_id' => $this->user_id,
            'photo' => $this->photo,
            'photo_url' => $this->photo ? url(env('APP_URL') . '/storage/' . $this->photo) : null,
            'orderNote' => $this->orderNote,
            'servicesType' => $this->servicesType,
            'sessions' => $this->sessions,
            'created_at'=> $this->created_at,
        ];
    }

    protected function transformBusinessTypes($businessTypes)
    {
        return array_map(function ($type) {
            return [
                'id' => $type,
                'name' => $this->businessTypes[$type] ?? 'Unknown',
            ];
        }, $businessTypes);
    }
}
