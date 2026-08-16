<?php

namespace App\Console\Commands;

use App\Models\Draft;
use App\Services\DraftService;
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
            ->orderBy('id')
            ->chunkById(100, function ($drafts) use ($draftService, &$expired) {
                foreach ($drafts as $draft) {
                    $before = $draft->status;
                    $state = $draftService->state($draft);

                    if ($before === 'live' && $state['status'] === 'expired') {
                        $expired++;
                    }
                }
            });

        $this->info("Expired {$expired} draft pick(s).");

        return self::SUCCESS;
    }
}
