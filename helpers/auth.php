<?php

use App\Models\SessionModel;
use Illuminate\Http\Request;
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

if (!function_exists('setTenantCentralSession')) {
  function setTenantCentralSession(Request $request, int|string|null $tenant_user_id = null)
  {
    $sessionModel = SessionModel::find($request->session()->getId());

    $result = DB::connection(config('database.connections.mysql.driver'))->table('sessions')->where('id', $request->session()->getId())->first();

    dd($result, $request->cookie());

    if (blank($sessionModel)) {
      return false;
    }

    if (tenant('id')) {
      $sessionModel->tenant_id = tenant('id');
      $sessionModel->user_id = $tenant_user_id;

      return $sessionModel->save();
    }

    return false;
  }
}
