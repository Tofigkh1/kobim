<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Training extends Model
{
    use HasFactory;

    protected $fillable = [
        "name",
        "date",
        "sme_id",
        "hour",
        "duration",
        "scope",
        "serviceType",
        "executive_id",
        "note",
        "link",
        "address",
        "includesBusiness",
        "certificate",
        "haveSkills",
        "status",
        "user_id",
        "photo",
        "orderNote",
        "servicesType", //drop
        "sessions"
    ];



    protected $casts = [
        'includesBusiness'=>'array',
        'scope'=>'array',
        'hour'=>'array',
        'sessions'=>'array'
    ];



    protected static function booted()
    {
    static::creating(function ($model) {
        $model->user_id = auth()->id();
    });

    }


    public function user():HasMany
    {
        return $this->hasMany(User::class);
    }

    public function executive()
    {
        return $this->belongsTo(User::class, 'executive_id');
    }
    
    public function applicationsAccepted()
    {
        return $this->hasMany(Application::class)
        ->where('status', 4);
    }

    public function applicationsSent()
    {
        return $this->hasMany(Application::class)
        ->where('status', 3);
    }



    public function applicationsCancelled()
    {
        return $this->hasMany(Application::class)
        ->where('status', 6);
    }




}
