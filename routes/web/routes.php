<?php

use Illuminate\Support\Facades\Route;

route_paths('common/docs');

Route::get('/', fn () => 'Ok');
