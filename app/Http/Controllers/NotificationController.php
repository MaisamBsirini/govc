<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Notification;

class NotificationController extends Controller
{
    public function getUserNotifications(Request $request)
    {
        $user = $request->user();

        $notifications = Notification::where('user_id', $user->id)
                            ->orderBy('created_at', 'desc')
                            ->get();

        return response()->json([
            'notifications' => $notifications
        ]);
    }
}
