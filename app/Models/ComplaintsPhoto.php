<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\belongsTo;

class ComplaintsPhoto extends Model
{
    protected $fillable = [
        'complaintID',
        'photo',
    ];

    public function complaint(){
        return $this->belongsTo(Complaint::class ,'complaintID');
    }

}
