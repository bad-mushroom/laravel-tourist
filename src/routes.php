<?php

use App\Models\User;
use BadMushroom\LaravelTourist\Facades\Tour;
use BadMushroom\LaravelTourist\Middleware\Tourism;
use Illuminate\Support\Facades\Route;

Route::get('tourist', function () {
    $user = User::find(rand(1, 9));

    Tour::visit($user);

    echo 'Hello from the Tourist package!';
})->middleware(Tourism::class);
