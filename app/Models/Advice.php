<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Advice extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'dateTime',
        'description',
        'category',
        'duration',
        'application_id',
        'excpert_id',
        'status',
        'contactInfo',
        'result',
    ];

}
