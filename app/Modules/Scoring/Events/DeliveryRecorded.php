<?php

namespace App\Modules\Scoring\Events;

use App\Models\MatchDelivery;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class DeliveryRecorded implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(public MatchDelivery $delivery)
    {
    }

    public function broadcastOn(): array
    {
        return [
            new Channel('matches.' . $this->delivery->match_id),
        ];
    }

    public function broadcastAs(): string
    {
        return 'delivery.recorded';
    }

    public function broadcastWith(): array
    {
        $match = $this->delivery->match;
        $innings = $match->innings()->whereKey($match->current_innings_id)->first();

        return [
            'delivery' => [
                'id' => $this->delivery->id,
                'over_number' => $this->delivery->over_number,
                'ball_number' => $this->delivery->ball_number,
                'runs_off_bat' => $this->delivery->runs_off_bat,
                'wides' => $this->delivery->wides,
                'no_balls' => $this->delivery->no_balls,
                'byes' => $this->delivery->byes,
                'leg_byes' => $this->delivery->leg_byes,
                'penalty_runs' => $this->delivery->penalty_runs,
                'total_runs' => $this->delivery->total_runs,
                'is_legal_delivery' => $this->delivery->is_legal_delivery,
                'notation' => $this->delivery->notation(),
                'commentary' => $this->delivery->commentary,
                'tts_commentary' => $this->delivery->ttsCommentary(),
                'wagon_x' => $this->delivery->wagon_x,
                'wagon_y' => $this->delivery->wagon_y,
            ],
            'match' => [
                'id' => $match->id,
                'status' => $match->status,
                'revision' => $match->revision,
                'total_runs' => $innings?->total_runs ?? 0,
                'wickets' => $innings?->wickets ?? 0,
                'legal_balls' => $innings?->legal_balls ?? 0,
            ]
        ];
    }
}
