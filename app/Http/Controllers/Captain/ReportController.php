<?php

namespace App\Http\Controllers\Captain;

use App\Http\Controllers\Controller;
use App\Models\Tournament;
use App\Modules\Analytics\Services\ReportService;
use Dompdf\Dompdf;
use Dompdf\Options;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\Response as SymfonyResponse;

class ReportController extends Controller
{
    public function __construct(private readonly ReportService $reportService)
    {
    }

    public function index(Tournament $tournament): View
    {
        $this->authorizeCaptain($tournament);

        return view('captain.reports.index', [
            'report' => $this->reportService->build($tournament, 'captain', request()->user()),
            'reportTypes' => $this->reportService->availableReportTypes('captain'),
        ]);
    }

    public function pdf(Tournament $tournament, string $type): SymfonyResponse
    {
        $this->authorizeCaptain($tournament);
        abort_unless(array_key_exists($type, $this->reportService->availableReportTypes('captain')), 404);

        $report = $this->reportService->build($tournament, 'captain', request()->user());
        $html = view('reports.pdf', [
            'report' => $report,
            'type' => $type,
            'title' => $this->reportService->availableReportTypes('captain')[$type],
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

    private function authorizeCaptain(Tournament $tournament): void
    {
        abort_unless(request()->user()->can('make draft pick'), 403);
        abort_unless($tournament->teams()->whereHas('captainAssignments', function ($query): void {
            $query->where('user_id', request()->user()->id)->whereNull('revoked_at');
        })->exists(), 403);
    }
}
