<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ComplaintsNote extends Model
{
    protected $fillable = [
        'complaintID',
        'note',
    ];

    public function complaint(){
        return $this->belongsTo(Complaint::class ,'complaintID');
    }

}
