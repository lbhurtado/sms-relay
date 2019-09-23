<?php

namespace App\Traits;

use App\CommandBus\Middlewares\{
    LogMiddleware, 
    EmailMiddleware, 
    ReplyMiddleware, 
    ForwardMiddleware, 
    RecordDiscussionMiddleware,
};

trait HasOptionalMiddlewares
{
    protected function log(bool $go = true)
    {
        ! $go || $this->addMiddleWare(LogMiddleware::class);

        return $this;
    }

    protected function email(bool $go = true)
    {
        ! $go || $this->addMiddleWare(EmailMiddleware::class);

        return $this;
    }

    protected function forward(bool $go = true)
    {
        ! $go || $this->addMiddleWare(ForwardMiddleware::class);

        return $this;
    }

    protected function reply(bool $go = true)
    {
        ! $go || $this->addMiddleWare(ReplyMiddleware::class);

        return $this;
    }

    protected function converse($go = true)
    {
        $this->addMiddleWare(RecordDiscussionMiddleware::class);

        return $this;
    }
}
