<?php

namespace App\Services;

use App\Models\Complaint;

class ReportService
{
    public function getComplaintsData()
    {
        // جلب كل الشكاوي مع المستخدم
        $complaints = Complaint::with('user')->get();

       
        return $complaints->map(function ($c) {
            return [
                'id' => $c->id,
                'user' => $c->user ? $c->user->name : null,
                'department' => $c->department,
                'status' => $c->status,
                'created_at' => $c->created_at->toDateTimeString(),
            ];
        });
    }
}
