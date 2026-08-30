@extends('errors::minimal')

@section('title', __('Not Found'))
@section('code', '404')
@section('message', __('Secret not found'))
@section('description', __('This link is invalid or the secret has already been removed. Retrying will not restore it.'))
