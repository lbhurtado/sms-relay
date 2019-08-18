@component('mail::message')
    Mobile **{{$mobile}}**,  {{-- use double space for line break --}}
    **{{$message}}**

    Click below to start working right now
    @component('mail::button', ['url' => $link])
        Go to your inbox
    @endcomponent
    Sincerely,
    SMS Relay team
@endcomponent
