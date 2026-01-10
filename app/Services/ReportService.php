<?php

namespace App\Services;

use App\Models\Complaint;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Response;

class ReportService
{
    public function exportComplaintsToCSV()
    {
        $complaints = Complaint::with('user')->get();

        $csv = "ID,User,Department,Status,Created At\n";
        foreach ($complaints as $c) {
            $csv .= "{$c->id},{$c->userID},{$c->department},{$c->status},{$c->created_at}\n";
        }

        return Response::make($csv, 200, [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename=\"complaints_report.csv\"',
        ]);
    }

    public function exportComplaintsToPDF()
    {
        $complaints = Complaint::with('user')->get();

        
        $pdf = Pdf::loadView('reports.complaints', compact('complaints'))
          ->setPaper('a4', 'portrait')
          ->setOption('isHtml5ParserEnabled', true)
          ->setOption('isRemoteEnabled', true);


        return $pdf->download('complaints_report.pdf');
    }
}
