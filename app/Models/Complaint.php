<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

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


    public function photos(): HasMany
    {
        return $this->hasMany(ComplaintsPhoto::class, 'complaintID', 'id');
    }


    public function notes(): HasMany
    {
        return $this->hasMany(ComplaintsNote::class, 'complaintID');
    }

}
