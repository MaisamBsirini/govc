<?php
namespace App\Repositories;

use App\Models\User;
use App\Models\Complaint;
use App\Models\ComplaintsPhoto;
use App\Models\ComplaintsNote;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class ComplaintRepository implements ComplaintRepositoryInterface
{
    public function createComplaint(array $data): Complaint
    {
        Cache::forget("citizen_complaints_{$data['userID']}");
        Cache::forget("all_complaints");
        return Complaint::create($data);
    }

    public function addPhoto(int $complaintID, string $path)
    {
        Cache::forget("complaint_{$complaintID}");
        return ComplaintsPhoto::create([
            'complaintID' => $complaintID,
            'photo' => $path
        ]);
    }

    public function addNote(int $complaintID, string $note)
    {
        Cache::forget("complaint_{$complaintID}");
        return ComplaintsNote::create([
            'complaintID' => $complaintID,
            'note' => $note
        ]);
    }

    public function getComplaintById(int $id): ?Complaint
    {
        return Cache::remember("complaint_{$id}", 60, function () use ($id) {
            return Complaint::where('id', $id)
                            ->with(['photos', 'notes'])
                            ->first();
        });
    }

    public function getComplaintsForCitizen(int $userID)
    {
        return Cache::remember("citizen_complaints_{$userID}", 60, function () use ($userID) {
            return Complaint::where('userID', $userID)->get();
        });
    }

    public function getComplaintsForEmployee(string $department)
    {
        return Cache::remember("employee_complaints_{$department}", 60, function () use ($department) {
            return Complaint::where('department', $department)
                            ->with(['photos', 'notes'])
                            ->get();
        });
    }

    public function getAllComplaints()
    {
        return Cache::remember("all_complaints", 60, function () {
            return Complaint::with(['photos', 'notes'])->get();
        });
    }

    public function updateStatus(int $complaintID, string $status): Complaint
    {
        return DB::transaction(function () use ($complaintID, $status) {
            $complaint = Complaint::where('id', $complaintID)
                                  ->lockForUpdate()
                                  ->firstOrFail();
            $complaint->status = $status;
            $complaint->save();

            Cache::forget("complaint_{$complaintID}");
            return $complaint;
        });
    }

    public function getCitizens()
    {
        return Cache::remember("citizens_list", 120, function () {
            return User::where('role', 'citizen')->get();
        });
    }

    public function getEmployees()
    {
        return Cache::remember("employees_list", 120, function () {
            return User::where('role', 'employee')->get();
        });
    }
}
