<?php

namespace App\Traits;

use App\Contact;
use Hashids\Hashids;
use Illuminate\Support\Arr;

trait HasHashes
{
    public static function hashids()
    {
        return new Hashids('', 4, config('vouchers.characters'));
    }

    public static function fromScratch(Contact $origin, string $message)
    {
        return tap(static::make(compact( 'message'))->contact()->associate($origin))->save();
    }

    public static function fromHash(string $ticket_id)
    {
        $id = Arr::get(static::hashids()->decode($ticket_id), 0);
        $ticket = static::findOrFail($id);

        return $ticket;
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
            $this->attributes['id'],
            $this->contact->id
        ];
    }
}
