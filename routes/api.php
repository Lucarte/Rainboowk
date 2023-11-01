<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;


/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

// // Example already written - uses "closure" request
// Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
//     return $request->user();
// });


// Routes pertaining to Rainboowk
// Show HOME (a list of the main-all-comprehensive catalog in all languages)
Route::get('/', [HomeController::class, 'getAll']);

// Get a specific book or a list of books depending on original language
Route::get('/{book_lan}', [BookController::class, 'list'])->where('book_lan', 'books|buecher|libros|livres');
Route::get('/{book_lan}/{id}', [BookController::class, 'getById'])->where('book_lan', 'books|buecher|libros|livres')->whereNumber('id');

// Get a specific author's info. + list of books written by them
Route::get('/author/{slug}', [AuthorController::class, 'getByFullname'])->where('slug', '[A-Za-z_]+');

// Get a specific illustrator's info. + list of books illustrated by them
Route::get('/illustrator/{slug}', [IllustratorController::class, 'getByFullname'])->where('slug', '[A-Za-z_]+');

// // Get a specific publisher's info. + list of books published by them
// Route::get('/publisher/{slug}', [PublisherController::class, 'getByFullname'])->where('slug', '[A-Za-z_]+');


// Infos about the project will be displayed here
Route::get('/about', [AboutController::class, 'index']);;


// All routes that deal with registration or login, or that need authetification will have the prefix 'auth'
Route::prefix('auth')->group(function () {
    // get Routes
    Route::get('/register', [RegisterController::class, 'index']);
    Route::get('/login', [LoginController::class, 'index']);
    Route::post('/register', RegisterController::class);
    Route::post('/login', [LoginController::class, 'create']);

    // Routes needing authentication
    Route::controller()->middleware('auth:sanctum')->group(function () {
        Route::post('/logout', LogoutController::class);

        // Route::controller(BookController::class)->group(function () {
        //     Route::post('/{book_lan}/create', 'create')->where('book_lan', 'books|buecher|libros|livres');;
        //     Route::patch('/{book_lan}/update/{id}', 'update')->where('book_lan', 'books|buecher|libros|livres')->whereNumber('id');
        //     Route::delete('/{book_lan}/delete/{id}', 'delete')->where('book_lan', 'books|buecher|libros|livres')->whereNumber('id');
        // });

        Route::controller(AuthorController::class)->group(function () {
            Route::post('/user/{username}/createAuthor', 'createAuthor');
            Route::patch('/user/{username}/updateAuthor/{slug}', 'updateAuthor')->where('slug', '[A-Za-z_]+');
            Route::delete('/user/{username}/deleteAuthor/{slug}', 'deleteAuthor')->where('slug', '[A-Za-z_]+');
        });

        Route::controller(IllustratorController::class)->group(function () {
            Route::post('/user/{username}/createIllustrator', 'createIllustrator');
            Route::patch('/user/{username}/updateIllustrator/{slug}', 'updateIllustrator')->where('slug', '[A-Za-z_]+');
            Route::delete('/user/{username}/deleteIllustrator/{slug}', 'deleteIllustrator')->where('slug', '[A-Za-z_]+');
        });

        // Route::controller(PublisherController::class)->group(function () {
        //     Route::post('/createPublisher', 'create');
        //     Route::patch('/updatePublisher/{slug}', 'update')->where('slug', '[A-Za-z_]+');
        //     Route::delete('/deletePublisher/{slug}', 'delete')->where('slug', '[A-Za-z_]+');
        // });

        // User possibilities: retrieve or update their info., or delete their profile
        Route::controller(UserController::class)->group(function () {
            // Need a route where 'admin' can see all users and if need be delete them
            Route::get('/users', 'users');
            Route::delete('/users', 'delete');

            // Routes for all other users not 'admin'
            Route::get('/user/{username}', 'getByUsername')->whereAlphaNumeric('username');
            Route::patch('/user/{username}', 'update')->whereAlphaNumeric('username');
            Route::delete('/user/{username}', 'delete')->whereAlphaNumeric('username');
        });
    });
});

// 404
Route::fallback(function () {
    return response()->json(['message' => 'Unbekantes Ziel'], 404);
});
