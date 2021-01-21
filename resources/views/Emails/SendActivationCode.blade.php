@component('mail::message')
# Verify your email address

Hi {{$email}}, <br />
Thanks for signing up to Master App.

To get access to your account please verify your email address by prividing given code.

@component('mail::panel')
    <b>{{$activationCode}}</b>
@endcomponent
Regards,<br>
{{ config('app.name') }}
@endcomponent