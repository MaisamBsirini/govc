<?php
namespace App\Observers;

use App\Models\Complaint;
use App\Services\FirebaseService;
use Illuminate\Support\Facades\Log;

class ComplaintObserver
{
    protected $firebase;

    public function __construct()
    {
        $this->firebase = new FirebaseService();
    }

    public function updated(Complaint $complaint)
    {
        // 1️⃣ تسجيل التعديل
        Log::info("User updated Complaint ID {$complaint->id}", [
            'status' => $complaint->status,
            'updated_at' => $complaint->updated_at,
        ]);

       event(new \App\Events\ComplaintUpdated($complaint));
    }
     public function created(Complaint $complaint)
    {
        Log::info("تم إنشاء شكوى #{$complaint->id} بواسطة المستخدم {$complaint->userID}");
         if ($complaint->user && $complaint->user->fcm_token) {
            $this->firebase->sendNotification(
                $complaint->user->fcm_token,
                'تم إنشاء شكوى   ',
                'الحالة الحالية: ' . $complaint->status,
                ['complaint_id' => $complaint->id]
            );
        }
    }

    public function deleted(Complaint $complaint)
    {
        Log::warning("تم حذف شكوى #{$complaint->id}");
    }
}
