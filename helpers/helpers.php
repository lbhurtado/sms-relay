<?php

if (! function_exists('redeem_regex')) {
    function redeem_regex() {
        $regex_code = '';
        tap(config('vouchers.characters'), function ($allowed) use (&$regex_code) {
            $regex_code = "([{$allowed}]{4})-([{$allowed}]{4})";
        });

        return [
            'regex_code' => $regex_code,
            'regex_email' => '[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,3})'
        ];
    }
}
