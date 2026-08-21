<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Tournament;
use App\Modules\Analytics\Services\ReportService;
use Dompdf\Dompdf;
use Dompdf\Options;
use Illuminate\Http\Response;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\Response as SymfonyResponse;

class ReportController extends Controller
{
    public function __construct(private readonly ReportService $reportService)
    {
    }

    public function index(Tournament $tournament): View
    {
        return view('admin.reports.index', [
            'report' => $this->reportService->build($tournament, 'admin', request()->user()),
            'reportTypes' => $this->reportService->availableReportTypes('admin'),
        ]);
    }

    public function pdf(Tournament $tournament, string $type): SymfonyResponse
    {
        abort_unless(array_key_exists($type, $this->reportService->availableReportTypes('admin')), 404);

        $report = $this->reportService->build($tournament, 'admin', request()->user());
        $html = view('reports.pdf', [
            'report' => $report,
            'type' => $type,
            'title' => $this->reportService->availableReportTypes('admin')[$type],
        ])->render();

        $options = new Options();
        $options->set('isRemoteEnabled', false);
        $dompdf = new Dompdf($options);
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();

        return response($dompdf->output(), 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'attachment; filename="'.str()->slug($tournament->slug.'-'.$type).'.pdf"',
        ]);
    }
}
