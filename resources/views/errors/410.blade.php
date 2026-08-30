@extends('errors::minimal')

@section('title', __('Secret Expired'))
@section('code', '410')
@section('message', __('Secret expired or consumed'))
@section('description', __('This secret was permanently deleted after expiring or being revealed. Retrying will not restore it.'))
