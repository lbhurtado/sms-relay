<?php

namespace App\Traits;

use Illuminate\Support\Arr;

trait HasEmail
{
    public function routeNotificationForMail()
    {
        return $this->email;
    }

    public function getEmailAttribute()
    {
        return Arr::get($this->extra_attributes, 'email');
    }

    public function setEmailAttribute($value)
    {
        Arr::set($this->extra_attributes, 'email', $value);
    }

    public function setEmail(string $email)
    {
        $this->update(compact('email'));

        return $this;
    }
}
