<?php

namespace App\Listeners;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use App\Events\ComplaintUpdated;
use App\Services\FirebaseService;

class SendComplaintNotification
{
    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
     public function handle(ComplaintUpdated $event)
    {
        $complaint = $event->complaint;
        if ($complaint->user && $complaint->user->fcm_token) {
            $firebase = new FirebaseService();
            $firebase->sendNotification(
                $complaint->user->fcm_token,
                'تم تحديث شكواك',
                'الحالة الحالية: ' . $complaint->status
            );
        }
    }
}
