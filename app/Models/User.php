<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, HasRoles;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'email_verified_at',
        "fullName",
        "sme_id",
        "education",
        "mainPlaceWork",
        "duty",
        "contactNumber",
        "contactEmail",
        "city",
        "address",
        "date",
        "serviceName",
        "duration",
        "serviceForm",
        "user_id",
        "status",
        "serviceType",
        "note",
        "trailer",
        "visibility",
        "fin",
        "photo",
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    public function isAdmin()
    {
        return $this->hasAnyRole(['super_admin', 'admin', 'developer','moderator']);
    }

    public function isSuperAdmin()
    {
       return $this->hasRole("super_admin");
    }

    public function isKobimAdmin()
    {
        return $this->hasAnyRole(['access','Admin - KOBIM','KOBIM - Admin','Admin - Kobim']);
    }

    public function applications(): HasMany
    {
        return $this->hasMany(Application::class);
    }

    

    public function sme():HasOne
    {
        return $this->hasOne(SmeInformation::class);
    }



    protected static function booted()
    {
        static::creating(function ($model) {
            $user = auth()->user();
            if ($user) {
                $model->user_id = $user->id;
                if (!$user->isAdmin()) {
                    $model->sme_id = $user->sme_id ?? null;
                }
            }
        });
    }
}
