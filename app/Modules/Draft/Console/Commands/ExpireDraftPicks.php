<?php

namespace App\Modules\Draft\Console\Commands;

use App\Models\Draft;
use App\Modules\Draft\Services\DraftService;
use Illuminate\Console\Command;

class ExpireDraftPicks extends Command
{
    protected $signature = 'drafts:expire-picks';

    protected $description = 'Mark live draft picks as expired when their server timer has elapsed';

    public function handle(DraftService $draftService): int
    {
        $expired = 0;

        Draft::query()
            ->where('status', 'live')
            ->with(['activePick.round'])
            ->get()
            ->each(function (Draft $draft) use ($draftService, &$expired): void {
                $pick = $draft->activePick;
                if ($pick && $pick->isExpiredNow()) {
                    $draftService->expireActivePick($draft);
                    $expired++;
                }
            });

        if ($expired > 0) {
            $this->info("Expired {$expired} draft pick(s).");
        }

        return Command::SUCCESS;
    }
}
