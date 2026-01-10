<?php
namespace App\Http\Controllers;

use App\Services\ReportService;
use Illuminate\Http\JsonResponse;

class ReportController extends Controller
{
    protected $reportService;

    public function __construct(ReportService $reportService)
    {
        $this->reportService = $reportService;
    }

    public function exportCSV(): JsonResponse
    {
        $data = $this->reportService->getComplaintsData();
        return response()->json([
            'message' => 'Complaints data retrieved successfully',
            'data' => $data
        ]);
    }

    public function exportPDF(): JsonResponse
    {


        $data = $this->reportService->getComplaintsData();
        return response()->json([
            'message' => 'Complaints data retrieved successfully',
            'data' => $data
        ]);
    }
}
