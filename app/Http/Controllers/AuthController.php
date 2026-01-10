<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use App\Mail\OtpMail;
use App\Models\User;

class AuthController extends Controller
{
    public function register(Request $request){

        $validated = $request->validate([
            'name' => 'required',
            'email' => 'required|email|unique:users',
            'phone' => 'nullable|unique:users',
            'password' => 'required|min:6'
        ]);

        $otp = rand(100000, 999999);

        DB::beginTransaction();
        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'] ,
            'phone' => $validated['phone'] ?? null,
            'password' => bcrypt($validated['password']),
            'otp_code' => $otp,
            'otp_expires_at' => now()->addMinutes(10),
            'role' => 'citizen'
        ]);

        $user->save();
        DB::commit();

        // SEND EMAIL
        Mail::to($user->email)->send(new OtpMail($otp));

        return response()->json([
            'message' => 'Account created, please check your email for OTP.',
            'email' => $user->email,
        ]);

    }


    public function verifyOtp(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'otp' => 'required'
        ]);

        $user = User::where('email', $request->email)->first();

        if (!$user) {
            return response()->json(['message' => 'User not found'], 404);
        }

        if ($user->otp_code != $request->otp) {
            return response()->json(['message' => 'Incorrect OTP'], 400);
        }

        if (now()->greaterThan($user->otp_expires_at)) {
            return response()->json(['message' => 'OTP expired'], 400);
        }

        $user->update([
            'otp_code' => null,
            'otp_expires_at' => null,
            'is_verified' => true,
            'fcm_token' => $request->fcm_token,
        ]);

        return response()->json(['message' => 'Account verified successfully']);
    }

public function login(Request $request)
{
    $request->validate([
        'email' => 'required|email',
        'password' => 'required'
    ]);

    $user = User::where('email', $request->email)->first();

    // تسجيل محاولة فاشلة
    if (!$user || !Hash::check($request->password, $user->password)) {

        // زيادة عداد المحاولات
        $attempts = cache()->increment("login_attempts_{$request->email}");

        // تحقق إذا تجاوزت 5 محاولات لأول مرة
        $lockNotified = cache()->get("lock_notified_{$request->email}", false);

        if ($attempts >= 5 && $user && !$lockNotified) {
            // قفل الحساب مؤقتاً
            $user->update(['locked_until' => now()->addMinutes(15)]);

            // تخزين الإشعار في قاعدة البيانات
            \App\Models\Notification::create([
                'user_id' => $user->id,
                'title' => 'محاولة دخول فاشلة',
                'body' => 'تم قفل حسابك مؤقتاً بسبب عدد محاولات تسجيل الدخول الفاشلة.',
                'data' => ['email' => $request->email, 'type' => 'login_failure'],
            ]);

            // إرسال إشعار عبر Firebase
            $firebase = new \App\Services\FirebaseService();
            $firebase->sendNotification(
                $user->fcm_token,
                'محاولة دخول فاشلة',
                'تم قفل حسابك مؤقتاً بسبب عدد محاولات تسجيل الدخول الفاشلة.'
            );

            // علامة لمنع إعادة الإشعار
            cache()->put("lock_notified_{$request->email}", true, now()->addMinutes(15));
        }

        return response()->json(['message' => 'Invalid credentials'], 401);
    }

    // تحقق من القفل
    if ($user->locked_until && now()->lessThan($user->locked_until)) {
        return response()->json([
            'message' => "تم قفل الحساب مؤقتاً. حاول لاحقاً بعد " . $user->locked_until->diffForHumans()
        ], 423);
    }

    // إعادة تعيين العد عند تسجيل الدخول الناجح
    cache()->forget("login_attempts_{$request->email}");
    cache()->forget("lock_notified_{$request->email}");

    if (!$user->is_verified) {
        return response()->json(['message' => 'Please verify your account'], 403);
    }

    $token = $user->createToken('mobile_token')->plainTextToken;

    return response()->json([
        'message' => 'Login successful',
        'token' => $token,
        'role' => $user->role,
        'user' => $user
    ]);
}



    public function createAccount(Request $request){
        $validated = $request->validate([
            'name' => 'required',
            'phone' => 'required|unique:users',
            'password' => 'required|min:6',
            'department' => 'required|in:Interior,Health,Education,Justice,AntiCorruption,Communications,Labor,ConsumerProtection',
        ]);

        $user = User::create([
            'name' => $validated['name'],
            'phone' => $validated['phone'],
            'password' => bcrypt($validated['password']),
            'role' => 'employee',
            'department' => $validated['department'],
        ]);

        $user->save();

        return response()->json([
            'message' => 'Account Created Successfully',
        ]);
    }

    public function employeeLogin(Request $request){
        $request->validate([
            'phone' => 'required',
            'password' => 'required|min:6'
        ]);

        $user = User::where('phone', $request->phone)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json(['message' => 'Invalid credentials'], 401);
        }

        $token = $user->createToken('employee_token')->plainTextToken;

        return response()->json([
            'message' => 'Login successful',
            'token' => $token,
            'role' => $user->role,
            'user' => $user
        ]);
    }


    public function deleteAccount($userID)
    {
        $user = User::where('id', $userID)->get()->first();

        $user->tokens()->delete();

        $user->delete();

        return response()->json([
            'message' => 'Account deleted successfully'
        ], 200);
    }

}
