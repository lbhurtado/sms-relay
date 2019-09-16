<?php

use App\Contact;
use Faker\Generator as Faker;

$factory->define(Contact::class, function (Faker $faker) {

    return [
        'mobile' => $faker->mobilePhone,
    ];
});
