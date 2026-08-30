@extends('errors::minimal')

@section('title', __('Service Unavailable'))
@section('code', '503')
@section('message', __('Wisp is temporarily unavailable'))
@section('description', __('The service is taking a short break. Retrying later is the safest next step.'))
