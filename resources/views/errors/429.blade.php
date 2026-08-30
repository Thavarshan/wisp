@extends('errors::minimal')

@section('title', __('Too Many Requests'))
@section('code', '429')
@section('message', __('Too many attempts'))
@section('description', __('Please wait a moment before trying again. Your secret remains protected while access is limited.'))
