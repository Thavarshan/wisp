@extends('errors::minimal')

@section('title', __('Secret Expired'))
@section('code', '410')
@section('message', __('Unfortunately, the secret you are trying to access has expired.'))
