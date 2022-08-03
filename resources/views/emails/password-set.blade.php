@component('mail::message')
# {{ __('passwords.notifications.password_set.title', ['tenant' => $tenant]) }}

{{ __('passwords.notifications.password_set.message', ['tenant' => $tenant]) }}

@component('mail::button', ['url' => $url])
{{ __('passwords.notifications.password_set.button') }}
@endcomponent

{{ 
  __(
    'passwords.notifications.password_set.expiry',
    [
      'count' => config('auth.passwords.users.expire'), 
      'url' => $request_link 
    ])
}}

{{ __('passwords.notifications.salutation') }},<br>
{{ config('filament.brand') }}
@endcomponent