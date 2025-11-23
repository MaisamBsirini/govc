<?php

namespace App\DAO;

use App\Models\User;
use App\Models\Complaint;
use App\Models\ComplaintsPhoto;
use App\Models\ComplaintsNote;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;


class ComplaintDAO
{
    public function createComplaint(array $data)
    {
        Cache::forget("citizen_complaints_{$data['userID']}");
        Cache::forget("all_complaints");
        return Complaint::create($data);
    }

    public function addPhoto($complaintID, $path)
    {
        Cache::forget("complaint_{$complaintID}");
        return ComplaintsPhoto::create([
            'complaintID' => $complaintID,
            'photo' => $path
        ]);
    }

    public function addNote($complaintID, $note)
    {
        Cache::forget("complaint_{$complaintID}");
        return ComplaintsNote::create([
            'complaintID' => $complaintID,
            'note' => $note
        ]);
    }

    public function getComplaintById($id)
    {
        return Cache::remember("complaint_{$id}", 60, function () use ($id) {
            return Complaint::where('id', $id)
                            ->with(['photos', 'notes'])
                            ->first();
        });
    }

    public function getComplaintsForCitizen($userID)
    {
        return Cache::remember("citizen_complaints_{$userID}", 60, function () use ($userID) {
            return Complaint::where('userID', $userID)->get();
        });
    }

    public function getComplaintsForEmployee($department)
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

    public function updateStatus($complaintID, $status)
    {
        return DB::transaction(function () use ($complaintID, $status) {

            $complaint = Complaint::where('id', $complaintID)
                                ->lockForUpdate()                        // Concurrent Access
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
        return Cache::remember("citizens_list", 120, function () {
            return User::where('role', 'employee')->get();
        });
    }
}
