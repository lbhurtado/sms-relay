<?php

namespace App\CommandBus\Middlewares;

use App\Ticket;
use App\Classes\SupportStage;
use League\Tactician\Middleware;
use App\Exceptions\MaximumApproachesReached;

class ConfineMiddleware implements Middleware
{
    public function execute($command, callable $next)
    {
        $approaches = Ticket::where('contact_id', $command->origin->id)
            ->whereHas('statuses', function($q) {
                return $q->where('name', '!=', SupportStage::CLOSED());
            })
            ->count();
        $maximum = config('sms-relay.approach.maximum');

        if ($approaches >= $maximum){
            throw new MaximumApproachesReached(".");
        }

        $next($command);
    }
}
