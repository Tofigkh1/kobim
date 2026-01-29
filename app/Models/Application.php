<?php

namespace App\Models;

use App\Observers\ApplicationObserver;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Application extends Model
{
    use HasFactory;
    protected $model = Application::class;


    protected $fillable = [
        "voen",
        "fin",
        "voenPersonName",
        "applicationNumber",
        "voenMeyar",
        "voenAddress",
        "voenActivityName",
        "voenFieldActivity",
        "voenContactInfo",
        "fieldActivity",
        "fieldWantAct",
        "otherFieldActivity",
        "actualResidentialAddress",
        "employeeType",
        "fullName",
        "actualCity",
        "actualAddress",
        "education",
        "mainPlaceWork",
        "duty",
        "contactNumber",
        "contactEmail",
        "signatureNumber",
        "city",
        "address",
        "date",
        "serviceName",
        "duration",
        "serviceForm",
        "status",
        "statusNote",
        "serviceType",
        "note",
        "trailer",
        "user_id",
        "accepted_user_id",
        "employeeCount",
        "training_id",
        "advice_id", // drop
        "networking_id", // drop
        "finPlaceOfBirth",
        "finRegistrationAddress",
        "finbirthday",
        "finGender",
        "finAge",
        "sme_id"
    ];

    protected $casts = [
        'voenContactInfo' => 'array',
    ];

    protected static function booted()
    {
    static::creating(function ($model) {
        $model->user_id = auth()->id();
    });

    static::observe(ApplicationObserver::class);

    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function employeeTasks()
    {
        return $this->hasMany(EmployeeTask::class);
    }
}

 
