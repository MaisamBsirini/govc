<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Traits\HasVersioning;

class Complaint extends Model
{
    use HasVersioning;

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

     public function user()
    {
        return $this->belongsTo(User::class, 'userID'); 

    }



}
