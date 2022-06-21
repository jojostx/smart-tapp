<?php

use Illuminate\Support\Facades\Auth;

if (!function_exists('admin')) {
  function admin()
  {
    return Auth::guard('admin')->user();
  }
}

if (!function_exists('authenticatedTenant')) {
  function authenticatedTenant(): bool
  {
    if (blank(tenant()) && session()->has('_token') && blank(request()->user())) {
      return true;
    }

    return false;
  }
}
