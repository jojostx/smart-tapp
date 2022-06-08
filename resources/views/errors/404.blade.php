@extends('errors.illustrated-layout')

@section('title', __('Not Found'))

@section('code', '404')

@section('message')
  <p class="text-2xl md:text-3xl font-light leading-normal">Sorry we couldn't find this page. </p>
  <p class="mb-8">But don't worry, you can find plenty of other things on our homepage.</p>
@endsection