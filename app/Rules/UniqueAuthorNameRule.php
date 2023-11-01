<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Support\Str;
use App\Models\Author;

class UniqueAuthorNameRule implements ValidationRule
{
    public function validate(string $attribute, $value, Closure $fail): void
    {
        $first_name = Str::lower(request('first_name')); // Convert input to lowercase
        $last_name = Str::lower(request('last_name'));   // Convert input to lowercase

        // Check if an author with the same name (case-insensitive) exists
        $existingAuthor = Author::where([
            ['first_name', '=', $first_name],
            ['last_name', '=', $last_name],
        ])->first();

        if ($existingAuthor) {
            $fail("The :attribute has already been taken.");
        }
    }
}
