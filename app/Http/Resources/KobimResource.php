<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class KobimResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'smeName' => $this->smeName,
            'smeLocation' => $this->smeLocation,
            'contactNumber' => $this->contactNumber,
            'contactEmail' => $this->contactEmail,
            'website' => $this->website,
            'teamLeader_id' => $this->teamLeader_id,
            'user_id' => $this->user_id,
            'executiveCompany' => $this->executiveCompany,
            'voen' => $this->voen,
            'icon_url' => $this->icon ? url(env('APP_URL') . '/storage/' . $this->icon) : null,
            'facebook' => $this->facebook,
            'instagram' => $this->instagram,
            'youtube' => $this->youtube,
            'linkedin' => $this->linkedin,
            'teamLeaderName' => $this->teamLeaderName,
        ];
    }
}
