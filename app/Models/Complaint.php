<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Complaint extends Model
{
    protected $fillable = [
        'userID',
        'type',
        'department',
        'location',
        'description',
        'status',
    ];


    public function ComplaintsPhoto(): HasMany
    {
        return $this->hasMany(ComplaintsPhoto::class, 'complaintID');
    }

    public function ComplaintsNote(): HasMany
    {
        return $this->hasMany(ComplaintsNote::class, 'complaintID');
    }

}
