<?php

use App\Models\User;
use BadMushroom\Tourist\Facades\Tour;
use BadMushroom\Tourist\Middleware\Tourism;
use Illuminate\Support\Facades\Route;

Route::get('tourist', function () {
    $user = User::find(rand(1, 9));

    Tour::visit($user);

    echo 'Hello from the Tourist package!';
})->middleware(Tourism::class);
