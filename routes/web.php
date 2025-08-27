<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('soon');
});

Route::get('/privacy', function () {
    return view('public.privacy');
});
