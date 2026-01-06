<?php
namespace App\Repositories;

use App\Models\Complaint;

interface ComplaintRepositoryInterface
{
    public function createComplaint(array $data): Complaint;
    public function addPhoto(int $complaintID, string $path);
    public function addNote(int $complaintID, string $note);
    public function getComplaintById(int $id): ?Complaint;
    public function getComplaintsForCitizen(int $userID);
    public function getComplaintsForEmployee(string $department);
    public function getAllComplaints();
    public function updateStatus(int $complaintID, string $status): Complaint;
    public function getCitizens();
    public function getEmployees();
}
