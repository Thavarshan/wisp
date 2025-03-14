<?php

use Illuminate\Support\Facades\Route;

Route::get('/docs', fn () => view('docs.index'));
