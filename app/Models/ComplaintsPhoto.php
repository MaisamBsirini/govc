<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ComplaintsPhoto extends Model
{
    protected $fillable = [
        'ComplaintID',
        'photo',
    ];

    public function complaint(){
        return $this->belongsTo(Complaint::class ,'complaintID');
    }

}
