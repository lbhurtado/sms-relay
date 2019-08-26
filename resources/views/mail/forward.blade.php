@component('mail::message')
    Mobile **{{$sms->from}}**,  {{-- use double space for line break --}}
    **{{$sms->getMessage()}}**

    Click below to start working right now
    @component('mail::button', ['url' => $link])
        Go to your inbox
    @endcomponent
    Sincerely,
    SMS Relay team
@endcomponent
