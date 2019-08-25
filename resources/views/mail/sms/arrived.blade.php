@component('mail::message')
# SMS Alert

From: {{$sms->from}}
To: {{$sms->to}}
Message {{$sms->message}}

@component('mail::button', ['url' => ''])
Button Text
@endcomponent

Thanks,<br>
{{ config('app.name') }}
@endcomponent
