<?php

namespace App\CommandBus\Middlewares;

use App\Ticket;
use App\Classes\SupportStage;
use League\Tactician\Middleware;
use App\Exceptions\MaximumApproachesNotReachedException;

class CheckMaximumApproachesNotReachedMiddleware implements Middleware
{
    public function execute($command, callable $next)
    {
        $approaches = Ticket::where('contact_id', $command->origin->id)
            ->otherCurrentStatus([SupportStage::RESOLVED(), SupportStage::CLOSED()])
            ->count();
        $maximum = config('sms-relay.approach.maximum');

        if ($approaches >= $maximum){
            throw new MaximumApproachesNotReachedException("Maximum approaches not reached.");
        }

        $next($command);
    }
}
