<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\Tournament;
use App\Services\ReportService;
use Dompdf\Dompdf;
use Dompdf\Options;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\Response as SymfonyResponse;

class ReportController extends Controller
{
    public function __construct(private readonly ReportService $reportService)
    {
    }

    public function show(Tournament $tournament): View
    {
        $this->authorizePublic($tournament);

        return view('public.reports.show', [
            'report' => $this->reportService->build($tournament, 'public'),
            'reportTypes' => $this->reportService->availableReportTypes('public'),
        ]);
    }

    public function pdf(Tournament $tournament, string $type): SymfonyResponse
    {
        $this->authorizePublic($tournament);
        abort_unless(array_key_exists($type, $this->reportService->availableReportTypes('public')), 404);

        $report = $this->reportService->build($tournament, 'public');
        $html = view('reports.pdf', [
            'report' => $report,
            'type' => $type,
            'title' => $this->reportService->availableReportTypes('public')[$type],
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

    private function authorizePublic(Tournament $tournament): void
    {
        abort_unless($tournament->is_public && in_array($tournament->status, ['live', 'completed'], true), 404);
    }
}
