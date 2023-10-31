<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Validator;

class AppServiceProvider extends ServiceProvider
{
    public function boot()
    {
        Validator::extend('isbn', function ($attribute, $value) {
            $value = str_replace(['-', ' '], '', $value);
            return (strlen($value) === 13) && ctype_digit($value);
        });

        Validator::extend('printDate', function ($attribute, $value) {
            if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $value)) {
                throw new \Exception("=> Datum muss folgendes Format haben: YYYY-MM-DD (z.B. 2023-03-15)");
            }
        });
    }



    public function register()
    {
        //
    }
}
