<?php

namespace App;

use Hashids\Hashids;
use Illuminate\Database\Eloquent\Model;

class Ticket extends Model
{
    protected $fillable = [
        'title', 'message', 'status'
    ];

    protected $attributes = [
       'status' => 'open',
    ];

    public static function generate(Contact $contact, string $title, string $message)
    {
    	return tap(static::make(compact('title', 'message'))
    		->contact()->associate($contact)
    	)->save();
    }

    public static function getHasher()
    {
    	return new Hashids('', 4, config('vouchers.characters'));
    }

    public function assignTicketId()
    {
    	 tap(static::getHasher(), function ($hashids) {
    		$ticket_id = $hashids->encode($this->id, $this->contact->id);
    		static::unguard();
    		$this->update(compact('ticket_id'));
    		static::reguard(); 	
    	 });

    	return $this;
    }

    public function contact()
    {
        return $this->belongsTo(Contact::class);
    }
}