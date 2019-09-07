<?php

namespace App;

use Hashids\Hashids;
use Illuminate\Support\Arr;
use Spatie\ModelStatus\HasStatuses;
use Illuminate\Database\Eloquent\Model;
use App\Events\{TicketEvents, TicketEvent};

class Ticket extends Model
{
    use HasStatuses;

    protected $fillable = [
        'message'
    ];

    public static function open(Contact $contact, string $message)
    {
    	$ticket = tap(static::make(compact( 'message'))->contact()->associate($contact))
            ->save()
            ->setStatus('open', 'initial')
        ;

        event(TicketEvents::OPENED, new TicketEvent($ticket));

        return $ticket;
    }

    public static function respond(Contact $contact, string $ticket_id, string $message)
    {
        $id = Arr::get(static::hashids()->decode($ticket_id), 0);
        $ticket = static::findOrFail($id);
        $ticket->setStatus('update', $message);

        event(TicketEvents::UPDATED, (new TicketEvent($ticket))->setResponder($contact));

        return $ticket;
    }

    public static function hashids()
    {
    	return new Hashids('', 4, config('vouchers.characters'));
    }

    public function generateHashIds()
    {
    	 tap(static::hashids(), function ($hashids) {
    		$ticket_id = $hashids->encode($this->getCompositeKeys());
    		static::unguard();
    		$this->update(compact('ticket_id'));
    		static::reguard();
    	 });

    	return $this;
    }

    protected function getCompositeKeys()
    {
        return [
            $this->id,
            $this->contact->id
        ];
    }

    public function contact()
    {
        return $this->belongsTo(Contact::class);
    }
}
