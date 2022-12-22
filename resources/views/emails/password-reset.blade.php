@component('mail::message')
# {{ __('passwords.notifications.password_reset.title', ['host' => $host]) }}

{{ __('passwords.notifications.password_reset.message', ['host' => $host]) }}

@component('mail::button', ['url' => $url])
{{ __('passwords.notifications.password_reset.button') }}
@endcomponent

{{ __('passwords.notifications.password_reset.expiry', ['count' => config('auth.passwords.users.expire')]) }}

{{ __('passwords.notifications.salutation') }},<br>
{{ config('filament.brand') }}
@endcomponent