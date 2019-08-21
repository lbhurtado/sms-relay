<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Hashtag extends Model
{
    protected $fillable = [
        'tag',
    ];

    public function contact()
    {
        return $this->belongsTo(Contact::class);
    }
}
