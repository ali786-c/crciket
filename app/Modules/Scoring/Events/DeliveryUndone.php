<?php

namespace App\Modules\Scoring\Events;

use App\Models\CricketMatch;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class DeliveryUndone implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(public CricketMatch $match)
    {
    }

    public function broadcastOn(): array
    {
        return [
            new Channel('matches.' . $this->match->id),
        ];
    }

    public function broadcastAs(): string
    {
        return 'delivery.undone';
    }

    public function broadcastWith(): array
    {
        $innings = $this->match->innings()->whereKey($this->match->current_innings_id)->first();

        return [
            'match' => [
                'id' => $this->match->id,
                'status' => $this->match->status,
                'revision' => $this->match->revision,
                'total_runs' => $innings?->total_runs ?? 0,
                'wickets' => $innings?->wickets ?? 0,
                'legal_balls' => $innings?->legal_balls ?? 0,
            ]
        ];
    }
}
