@extends('errors::minimal')

@section('title', __('Page Expired'))
@section('code', '419')
@section('message', __('Session expired'))
@section('description', __('Your session is no longer valid. Returning to Wisp will start a safe fresh session.'))
