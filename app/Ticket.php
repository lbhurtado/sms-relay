<?php

namespace App;

use App\Traits\HasHashes;
use App\Classes\SupportStage;
use Spatie\ModelStatus\HasStatuses;
use Illuminate\Database\Eloquent\Model;
use App\Events\{TicketEvents, TicketEvent};


class Ticket extends Model
{
    use HasStatuses, HasHashes;

    //TODO - create duplicate middleware

    protected $fillable = [
        'message'
    ];

    public static function open(Contact $origin, string $message)
    {
    	return self::fromScratch($origin, $message)->setStage(SupportStage::OPENED());
    }

    public function endorse()
    {
        return $this->setStage(SupportStage::ENDORSED());
    }

    public function approach()
    {
        return $this->setStage(SupportStage::PENDING());
    }

    public function respond(Contact $responder, string $message)
    {
        return $this->setStage(SupportStage::HANDLED(), $responder, $message);
    }

    public function setStage(SupportStage $stage, Contact $responder = null, string $message = null)
    {
        $this->setStatus($stage);

        switch ($stage) {
            case SupportStage::OPENED:
                event(TicketEvents::OPENED, new TicketEvent($this));
                break;
            case SupportStage::ENDORSED:
                event(TicketEvents::ENDORSED, new TicketEvent($this));
                break;
            case SupportStage::HANDLED:
                event(TicketEvents::UPDATED, (new TicketEvent($this))->setResponder($responder)->setMessage($message));
                break;
        }

        return $this;
    }

    public function contact()
    {
        return $this->belongsTo(Contact::class);
    }
}
