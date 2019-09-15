<?php

namespace App;

use App\Traits\HasHashes;
use App\SMSTicket as Pivot;
use App\Classes\SupportStage;
use LBHurtado\Missive\Models\SMS;
use Spatie\ModelStatus\HasStatuses;
use Illuminate\Database\Eloquent\Model;
use App\Events\{TicketEvents, TicketEvent};

class Ticket extends Model
{
    use HasStatuses, HasHashes;

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

    public function converse(Contact $contact, string $message)
    {
        return $this->setStage(SupportStage::PENDING());
    }

    public function resolve(Contact $responder, string $message)
    {
        return $this->setStage(SupportStage::RESOLVED(), $responder, $message);
    }

    public function setStage(SupportStage $stage, Contact $responder = null, string $message = null)
    {
        if ($this->latestStatus($stage) == null) {
            $this->setStatus($stage);
        }

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
            case SupportStage::RESOLVED:
                event(TicketEvents::RESOLVED, (new TicketEvent($this))->setResponder($responder)->setMessage($message));
                break;
        }

        return $this;
    }

    public function contact()
    {
        return $this->belongsTo(Contact::class);
    }

    public function smss()
    {
        return $this->belongsToMany(SMS::class)
            // ->withPivot('contact_id')
            ->using(Pivot::class)
            ->withTimestamps();
    }

    public function addSMS(SMS $sms, array $attributes = [])
    {
        $this->smss()->attach($sms, $attributes);

        return $this;
    }
}
