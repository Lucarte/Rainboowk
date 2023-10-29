<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Validator;

class AppServiceProvider extends ServiceProvider
{
    public function boot()
    {
        // could add $attribute before $value if I were to use this function on other fields as well
        Validator::extend('isbn', function ($value) {
            $value = str_replace(['-', ' '], '', $value);
            return (strlen($value) === 13) && ctype_digit($value);
        });
    }

    public function register()
    {
        //
    }
}
