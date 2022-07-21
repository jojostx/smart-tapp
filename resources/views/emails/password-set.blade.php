@component('mail::message')
# {{ __('passwords.notifications.password_set.title', ['host' => $host]) }}

{{ __('passwords.notifications.password_set.message', ['host' => $host]) }}

@component('mail::button', ['url' => $url])
{{ __('passwords.notifications.password_set.button') }}
@endcomponent

{{ __('passwords.notifications.password_set.expiry', ['count' => config('auth.passwords.users.expire'), 'url' => route('filament.passwords.request')]) }}

{{ __('passwords.notifications.salutation') }},<br>
{{ config('filament.brand') }}
@endcomponent