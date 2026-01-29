<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EmployeeTask extends Model
{
    use HasFactory;


    protected $fillable = [
        "status",
        "serviceForm",
        "serviceType",
        "targetGroup",
        "user_id",
        "application_id",
        "date",
        "note",
        "attach",
        'training_id',
        "creator_id",
    ];

    
    public function user():BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function application():BelongsTo
    {
        return $this->belongsTo(Application::class);
    }
    

    public static function getStatusLabel(int $status): string
    {
        return match ($status) {
            0 => 'Yaradıldı',
            1 => 'Göndərildi',
            2 => 'Təyin edilib',
            3 => 'Planlanıb',
            4 => 'İcra olundu',
            5 => 'Ləğv edildi',
            6 => 'İştirak etmədi',
            default => 'Bilinməyən',
        };
    }

    /**
     * Status üçün rəng qaytaran metod.
     */
    public static function getStatusColor(int $status): string
    {
        return match ($status) {
            0 => 'gray',
            1 => 'info',
            2 => 'warning',
            3 => 'warning',
            4 => 'success',
            5 => 'danger',
            6 => 'danger',
            default => 'default',
        };
    }
}
