<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use App\Models\User;
use App\Models\Complaint;
use App\Models\ComplaintsNote;
use App\Models\ComplaintsPhoto;

class ComplaintController extends Controller
{
    // _____________ Citizen _______________

    public function addComplaint(Request $request)
    {
        $user = Auth::user();

        $validated = $request->validate([
            'type' => 'required|string',
            'description' => 'required|string',
            'department' => 'required|in:Interior,Health,Education,Justice,AntiCorruption,Communications,Labor,ConsumerProtection',
            'location' => 'required|string',
            'photos' => 'nullable',
            'photos.*' => 'image|mimes:jpg,jpeg,png|max:2048',
        ]);

        $complaint = Complaint::create([
            'userID' => $user->id,
            'type' => $validated['type'],
            'description' => $validated['description'],
            'department' => $validated['department'],
            'location' => $validated['location'],
            'status' => 'new'
        ]);

        $photoUrls = [];

        if ($request->hasFile('photos')) {
            $photos = $request->file('photos');

            if (!is_array($photos)) {
                $photos = [$photos];
            }

            foreach ($photos as $photo) {
                $path = $photo->store('complaints_photos', 'public');

                ComplaintsPhoto::create([
                    'complaintID' => $complaint->id,
                    'photo' => $path,
                ]);

                $photoUrls[] = asset('storage/' . $path);
            }
        }

        return response()->json([
            'message' => 'Complaint Created Successfully',
            'complaint' => [
                'id' => $complaint->id,
                'userID' => $complaint->userID,
                'type' => $complaint->type,
                'description' => $complaint->description,
                'department' => $complaint->department,
                'location' => $complaint->location,
                'status' => $complaint->status,
                'photos' => $photoUrls,
                'created_at' => $complaint->created_at,
                'updated_at' => $complaint->updated_at,
            ],
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

    public function getOneComplaint($id) {
        $complaint = Complaint::where('id',$id)->with(['photos', 'notes'])->get();

        return response()->json([
            'complaint' => $complaint
        ]);

    }



    // ____________ Employee ______________

    public function getComplaintsEmployee() {
        $user = Auth::user();

        $department = $user->department;

        $complaints = Complaint::where('department', $department)->with(['photos', 'notes'])->get();

        return response()->json([
            'department' => $department,
            'count' => $complaints->count(),
            'complaints' => $complaints
        ], 200);

    }


    public function updateStatus(Request $request, $id)
    {
        $user = Auth::user();

        $request->validate([
            'status' => 'nullable|string|in:new,inProgress,completed,rejected',
            'note'   => 'nullable|string',
        ]);

        $complaint = Complaint::findOrFail($id);

        // Update status if provided
        if ($request->filled('status')) {
            $complaint->status = $request->status;
            $complaint->save();
        }

        // Add note if provided
        if ($request->filled('note')) {
            ComplaintsNote::create([
                'complaintID' => $complaint->id,
                'note' => $request->note,
            ]);
        }

        // Load updated notes
        $complaint->load('notes');

        return response()->json([
            'message' => 'Complaint updated successfully',
            'complaint' => $complaint
        ]);
    }


    //_____________ Admin ________________

    public function getAllComplaints(){
        $complaints = Complaint::with(['photos', 'notes'])->get();

        return response()->json([
            'message' => 'All Complaints',
            'complaint' => $complaints
        ]);
    }

    public function getUsers(){
        $citizens = User::where('role', 'citizen')->get();
        $employees = User::where('role', 'employee')->get();

        return response()->json([
            'message' => 'All users',
            'citizens' => $citizens,
            'employees' => $employees
        ]);
    }

}
