@extends('errors::minimal')

@section('title', __('Server Error'))
@section('code', '500')
@section('message', __('Something went wrong'))
@section('description', __('The request failed unexpectedly. Do not repeat a reveal until you know whether it completed.'))
