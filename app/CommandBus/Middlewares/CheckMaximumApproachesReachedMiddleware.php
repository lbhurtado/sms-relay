<?php

namespace App\CommandBus\Middlewares;

use App\Ticket;
use App\Classes\SupportStage;
use League\Tactician\Middleware;
use App\Exceptions\MaximumApproachesReachedException;

class CheckMaximumApproachesReachedMiddleware implements Middleware
{
    public function execute($command, callable $next)
    {
        $approaches = Ticket::where('contact_id', $command->origin->id)
            ->otherCurrentStatus([SupportStage::RESOLVED(), SupportStage::CLOSED()])
            ->count();
        $maximum = config('sms-relay.approach.maximum');

        if ($approaches >= $maximum){
            throw new MaximumApproachesReachedException("Maximum approaches reached.");
        }

        $next($command);
    }
}
