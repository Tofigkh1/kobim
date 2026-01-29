<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SmeInformation extends Model
{
    use HasFactory;


    protected $fillable = [
        "smeName",
        "icon",
        "smeLocation",
        "contactNumber",
        "contactEmail",
        "website",
        "teamLeader_id",
        "user_id",
        "executiveCompany",
        "voen",
        "faceebook",
        "instagram",
        "youtube",
        "linkedin",
        "teamLeaderName",
    ];


    public function user():HasMany
    {
        return $this->hasMany(User::class);
    }

    protected static function booted()
    {
       static::creating(function ($model) {
           $model->user_id = auth()->id();
       });
    }

}
