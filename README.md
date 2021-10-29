# Laravel Tourist

A simple package for tracking unique visits, utm parameters, and model views.

## Installation

### Install Package

```
composer require bad-mushroom/laravel-tourist
```

### Initialize

```
php artisan migrate
```

The migration will create two new tables:
`tour_sessions`
`tour_visits`

### Register Middleware

In App\Http\Kernel,

```
'web' => ]
    ...
    \BadMushroom\Tourist\Middleware\Tourism::class,
];
```
or apply the middleware to a specifc route:

```
Route::get('/', function () {
    return view('welcome');
})->middleware('tourism');
```


### Configuration

Run `php artisan vendor:publish` to publish the `config/tourist.php` config file where you can modify some settings.

## Usage

### Traits

Adding the `BadMushroom\LaravelTourist\Tourable` trait to your models will will allow you to use the `visits` relationship.

### Facade

```
Tour::visit($model)
```

### Commands

Running `php artisan tourist::clear` will remove all expired tourist sessions and visits. YOu can canfigure how many days to keep data in the config file.
