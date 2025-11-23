<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use App\Models\User;
use App\Models\Complaint;
use App\Models\ComplaintsNote;
use App\Models\ComplaintsPhoto;

use App\DAO\ComplaintDAO;


class ComplaintController extends Controller
{
    protected $dao;

    public function __construct(ComplaintDAO $dao)
    {
        $this->dao = $dao;
    }


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

        $complaint = $this->dao->createComplaint([                     // DAO Called
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

                $this->dao->addPhoto($complaint->id, $path);                // DAO Called

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
        $complaints = $this->dao->getComplaintsForCitizen($user->id);

        return response()->json([
            'message' => 'All Complaints for user',
            'complaints' => $complaints
        ]);
    }

    public function getOneComplaint($id) {
        $complaint = $this->dao->getComplaintById($id);

        return response()->json([
            'complaint' => $complaint
        ]);
    }



    // ____________ Employee ______________

    public function getComplaintsEmployee() {
        $user = Auth::user();

        $department = $user->department;

        $complaints = $this->dao->getComplaintsForEmployee($department);

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

        // Update status through DAO
        if ($request->filled('status')) {
            $complaint = $this->dao->updateStatus($id, $request->status);
        } else {
            $complaint = $this->dao->getComplaintById($id);
        }



        // Add note through DAO
        if ($request->filled('note')) {
            $this->dao->addNote($id, $request->note);
        }

        // Reload updated complaint with notes
        $updated = $this->dao->getComplaintById($id);


        return response()->json([
            'message' => 'Complaint updated successfully',
            'complaint' => $updated
        ]);
    }


    //_____________ Admin ________________

    public function getAllComplaints(){
        $complaints = $this->dao->getAllComplaints();

        return response()->json([
            'message' => 'All Complaints',
            'complaint' => $complaints
        ]);
    }

    public function getUsers(){
        $citizens = $this->dao->getCitizens();
        $employees = $this->dao->getEmployees();

        return response()->json([
            'message' => 'All users',
            'citizens' => $citizens,
            'employees' => $employees
        ]);
    }

}
