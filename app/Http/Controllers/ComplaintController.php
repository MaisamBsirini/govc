<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Repositories\ComplaintRepositoryInterface;
use Illuminate\Support\Facades\Storage;
use App\Models\User;
use App\Models\Complaint;
use App\Models\ComplaintsNote;
use App\Models\ComplaintsPhoto;
use App\Services\FirebaseService;



class ComplaintController extends Controller
{
    
    protected $complaintRepo;

    public function __construct(ComplaintRepositoryInterface $complaintRepo)
        {
          $this->complaintRepo = $complaintRepo;}



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

        DB::beginTransaction();

        try{
        $complaint = $this->complaintRepo->createComplaint([                   
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

                $this->complaintRepo->addPhoto($complaint->id, $path);             

                $photoUrls[] = asset('storage/' . $path);
            }
        }
        DB::commit();
    }
 catch (\Exception $e) {
        DB::rollBack();
        return response()->json(['error' => 'حدث خطأ أثناء إنشاء الشكوى'], 500);
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
        $complaints = $this->complaintRepo->getComplaintsForCitizen($user->id);

        return response()->json([
            'message' => 'All Complaints for user',
            'complaints' => $complaints
        ]);
    }

    public function getOneComplaint($id) {
        $complaint = $this->complaintRepo->getComplaintById($id);

        return response()->json([
            'complaint' => $complaint
        ]);
    }



    // ____________ Employee ______________

    public function getComplaintsEmployee() {
        $user = Auth::user();

        $department = $user->department;

        $complaints = $this->complaintRepo->getComplaintsForEmployee($department);

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

    if ($request->filled('status')) {
        $complaint = $this->complaintRepo->updateStatus($id, $request->status);
    } else {
        $complaint = $this->complaintRepo->getComplaintById($id);
    }

    if ($request->filled('note')) {
        $this->complaintRepo->addNote($id, $request->note);
    }

    $updated = $this->complaintRepo->getComplaintById($id);


    return response()->json([
        'message' => 'Complaint updated successfully',
        'complaint' => $updated
    ]);
}




    //_____________ Admin ________________

    public function getAllComplaints(){
        DB::enableQueryLog();  
    $start = microtime(true);

    $complaints = $this->complaintRepo->getAllComplaints();

    $time = microtime(true) - $start;
    $queries = DB::getQueryLog();

        return response()->json([
            'message' => 'All Complaints',
            'complaint' => $complaints
        ]);
    }

    public function getUsers(){
        $citizens = $this->complaintRepo->getCitizens();
        $employees = $this->complaintRepo->getEmployees();

        return response()->json([
            'message' => 'All users',
            'citizens' => $citizens,
            'employees' => $employees
        ]);
    }

}
