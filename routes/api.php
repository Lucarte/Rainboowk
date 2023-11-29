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

//**//**//**//**//**//**//**//**//**//**//**//**//**//**//**//**//**//**//**//**//**//**//**//**
//**//**//**//**//**//**//**//**//**//**//**//**//**//**//**//**//**//**//**//**//**//**//**//**
//**//**//**//**//**//**//**//**//**//**//**//**//**//**//**//**//**//**//**//**//**//**//**//**
//**//**//**//     MAKE A GETALL AS ENDPOINT ALONE     //**//**//**//**//**//**//**//**//**//**//
//**//**//**//     LET HOMEPAGE GUIDE TO HOMEPORTAL    //**//**//**//**//**//**//**//**//**//**//
//**//**//**//**//**//**//**//**//**//**//**//**//**//**//**//**//**//**//**//**//**//**//**//**
//**//**//**//**//**//**//**//**//**//**//**//**//**//**//**//**//**//**//**//**//**//**//**//**
//**//**//**//**//**//**//**//**//**//**//**//**//**//**//**//**//**//**//**//**//**//**//**//**


// Show HOME (a list of the main-all-comprehensive catalog in all languages)
Route::get('/', [HomeController::class, 'getAll']);

// Get a specific book or a list of books
Route::get('/books', [BookController::class, 'list']);
Route::get('/book/{title}', [BookController::class, 'getByTitle']);

// Get a specific book or a list of books in spanish (libros)
Route::get('/libros', [LibroController::class, 'list']);
Route::get('/libro/{title}', [LibroController::class, 'getByTitle']);

// Get a specific book or a list of books in german (buecher)
Route::get('/buecher', [BuchController::class, 'list']);
Route::get('/buch/{title}', [BuchController::class, 'getByTitle']);

// // Get a specific book or a list of books in french (livres)
Route::get('/livres', [LivreController::class, 'list']);
Route::get('/livre/{title}', [LivreController::class, 'getByTitle']);

// Get a specific author's info. + list of books written by them
Route::get('/author/{slug}', [AuthorController::class, 'getByFullname'])->where('slug', '[A-Za-z_]+');

// Get a specific illustrator's info. + list of books illustrated by them
Route::get('/illustrator/{slug}', [IllustratorController::class, 'getByFullname'])->where('slug', '[A-Za-z_]+');

// Get a specific publisher's info. + list of books published by them
Route::get('/publisher/{slug}', [PublisherController::class, 'getByName'])->whereAlphaNumeric('name');


// Infos about the project will be displayed here
Route::get('/about', AboutController::class);;


// All routes that deal with registration or login, or that need authetification will have the prefix 'auth'
Route::prefix('auth')->group(function () {
    // get Routes
    Route::get('/register', [RegisterController::class, 'index']);
    Route::get('/login', [LoginController::class, 'index']);
    Route::post('/register', RegisterController::class);
    Route::post('/login', LoginController::class);

    // Routes needing authentication
    Route::controller()->middleware('auth:sanctum')->group(function () {
        Route::post('/user/logout', LogoutController::class);

        Route::controller(BookController::class)->group(function () {
            Route::post('/book/create', 'create');
            Route::patch('/book/update/{title}', 'update');
            Route::delete('/book/delete/{title}', 'delete');
        });

        Route::controller(LibroController::class)->group(function () {
            Route::post('/libro/create', 'create');
            Route::patch('/libro/update/{title}', 'update');
            Route::delete('/libro/delete/{title}', 'delete');
        });

        Route::controller(BuchController::class)->group(function () {
            Route::post('/buch/create', 'create');
            Route::patch('/buch/update/{title}', 'update');
            Route::delete('/buch/delete/{title}', 'delete');
        });

        Route::controller(LivreController::class)->group(function () {
            Route::post('/livre/create', 'create');
            Route::patch('/livre/update/{title}', 'update');
            Route::delete('/livre/delete/{title}', 'delete');
        });

        Route::controller(AuthorController::class)->group(function () {
            Route::post('/create_author', 'createAuthor');
            Route::patch('/update_author/{slug}', 'updateAuthor')->where('slug', '[A-Za-z_]+');
            Route::delete('/delete_author/{slug}', 'deleteAuthor')->where('slug', '[A-Za-z_]+');
        });

        Route::controller(IllustratorController::class)->group(function () {
            Route::post('/create_illustrator', 'createIllustrator');
            Route::patch('/update_illustrator/{slug}', 'updateIllustrator')->where('slug', '[A-Za-z_]+');
            Route::delete('/delete_illustrator/{slug}', 'deleteIllustrator')->where('slug', '[A-Za-z_]+');
        });

        Route::controller(PublisherController::class)->group(function () {
            Route::post('/create_publisher', 'createPublisher');
            Route::patch('/update_publisher/{name}', 'updatePublisher')->whereAlphaNumeric('name');;
            Route::delete('/delete_publisher/{name}', 'deletePublisher')->whereAlphaNumeric('name');;
        });

        Route::controller(CoverController::class)->group(function () {
            Route::delete('/delete_cover/{id}', 'deleteCover')->whereNumber('id');
            Route::post('/update_cover/{id}', 'updateCover')->whereNumber('id');
        });

        // User possibilities: retrieve or update their info., or delete their profile
        Route::controller(UserController::class)->group(function () {
            // 'admin' (set manually on DB) can see all users and if need be delete them
            Route::get('/users', 'usersList');

            // Routes for all but only 'admin' or owner can make changes
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
