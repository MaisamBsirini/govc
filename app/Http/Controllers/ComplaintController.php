<?php

namespace App\Http\Controllers\API;

use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Complaint;
use App\Models\ComplaintNote;
use Illuminate\Support\Str;

class ComplaintController extends Controller
{
    // تقديم شكوى جديدة (مواطن)
    public function addComplaint(Request $request)
    {
        $user = Auth::user();

        $request->validate([
            'type' => 'required|string',
            'description' => 'required|string',
            'department' => 'required|in:Interior, Health, Education, Justice, AntiCorruption, Communications, Labor, ConsumerProtection',
            'location' => 'required|string',
        ]);

        $complaint = Complaint::create([
            'user_id' => $user->id,
            'type' => $request->type,
            'description' => $request->description,
            'department' => $request->department,
            'location' => $request->location,
        ]);
        $complaint->save();


        return response()->json([
            'message' => 'Complaint Created Successfully',
            'complaint' => $complaint
        ], 201);

    }

    public function getComplaintsCitizen(){
        $user = Auth::user();
        $complaints = Complaint::where('userID', $user->id)->get();

        return response()->json([
            'message' => 'All Complaints for user',
            'complaints' => $complaints
        ]);
    }

    public function getOneComplaint($id)
    {
        $complaint = Complaint::findOrFail($id);

        return response()->json([
            'complaint' => $complaint
        ]);
    }


    // عرض الشكاوى حسب نوع المستخدم
    public function index()
    {
        $user = Auth::user();

        if ($user->role === 'citizen') {
            $complaints = Complaint::where('user_id', $user->id)->get();
        } elseif ($user->role === 'department') {
            $complaints = Complaint::where('department', $user->department)->get();
        } elseif ($user->role === 'admin') {
            $complaints = Complaint::all();
        } else {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        return response()->json($complaints);
    }

    // عرض شكوى واحدة
    public function show($id)
    {
        $complaint = Complaint::findOrFail($id);
        $user = Auth::user();

        // التحكم بالوصول
        if ($user->role === 'citizen' && $complaint->user_id !== $user->id) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        if ($user->role === 'department' && $complaint->department !== $user->department) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        return response()->json($complaint);
    }

    // الجهة الحكومية تغير حالة الشكوى
    public function updateStatus(Request $request, $id)
    {
        $user = Auth::user();

        if ($user->role !== 'department' && $user->role !== 'admin') {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $request->validate([
            'status' => 'required|string|in:new,in_progress,completed,rejected',
        ]);

        $complaint = Complaint::findOrFail($id);

        if ($user->role === 'department' && $complaint->department !== $user->department) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $complaint->status = $request->status;
        $complaint->save();

        return response()->json(['message' => 'Status updated', 'complaint' => $complaint]);
    }

    // إضافة ملاحظة على الشكوى
    public function addNote(Request $request, $id)
    {
        $user = Auth::user();

        if ($user->role !== 'department' && $user->role !== 'admin') {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $request->validate(['note' => 'required|string']);

        $complaint = Complaint::findOrFail($id);

        ComplaintNote::create([
            'complaint_id' => $complaint->id,
            'user_id' => $user->id,
            'note' => $request->note,
        ]);

        return response()->json(['message' => 'Note added successfully']);
    }
}
