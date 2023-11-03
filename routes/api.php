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

// Show HOME (a list of the main-all-comprehensive catalog in all languages)
Route::get('/', [HomeController::class, 'getAll']);

// Get a specific book or a list of books
Route::get('/books', [BookController::class, 'list']);
Route::get('/books/{title}', [BookController::class, 'getByTitle']);

// // Get a specific book or a list of books in spanish (libros)
// Route::get('/libros', [LibroController::class, 'list']);
// Route::get('/libros/{title}', [LibroController::class, 'getByTitle']);

// // Get a specific book or a list of books in german (buecher)
// Route::get('/buecher', [BuchController::class, 'list']);
// Route::get('/buecher/{title}', [BuchController::class, 'getByTitle']);

// // Get a specific book or a list of books in french (livres)
// Route::get('/livres', [LivreController::class, 'list']);
// Route::get('/livres/{title}', [LivreController::class, 'getByTitle']);

// Get a specific author's info. + list of books written by them
Route::get('/author/{slug}', [AuthorController::class, 'getByFullname'])->where('slug', '[A-Za-z_]+');

// Get a specific illustrator's info. + list of books illustrated by them
Route::get('/illustrator/{slug}', [IllustratorController::class, 'getByFullname'])->where('slug', '[A-Za-z_]+');

// Get a specific publisher's info. + list of books published by them
Route::get('/publisher/{slug}', [PublisherController::class, 'getByFullname'])->whereAlphaNumeric('name');


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
        Route::post('/user/logout', LogoutController::class);

        Route::controller(BookController::class)->group(function () {
            Route::post('/user/books/create', 'create');
            Route::patch('/user/books/update/{title}', 'update')->whereAlphaNumeric('title');
            Route::delete('/user/books/delete/{title}', 'delete')->whereAlphaNumeric('title');
        });

        // Route::controller(LibroController::class)->group(function () {
        //     Route::post('/libros/create', 'create');
        //     Route::patch('/libros/update/{title}', 'update')->whereAlphaNumeric('title');
        //     Route::delete('/libros/delete/{title}', 'delete')->whereAlphaNumeric('title');
        // });

        // Route::controller(BuchController::class)->group(function () {
        //     Route::post('/buecher/create', 'create');
        //     Route::patch('/buecher/update/{title}', 'update')->whereAlphaNumeric('title');
        //     Route::delete('/buecher/delete/{title}', 'delete')->whereAlphaNumeric('title');
        // });

        // Route::controller(LivreController::class)->group(function () {
        //     Route::post('/livres/create', 'create');
        //     Route::patch('/livres/update/{title}', 'update')->whereAlphaNumeric('title');
        //     Route::delete('/livres/delete/{title}', 'delete')->whereAlphaNumeric('title');
        // });

        Route::controller(AuthorController::class)->group(function () {
            Route::post('/user/create_author', 'createAuthor');
            Route::patch('/user/update_author/{slug}', 'updateAuthor')->where('slug', '[A-Za-z_]+');
            Route::delete('/user/delete_author/{slug}', 'deleteAuthor')->where('slug', '[A-Za-z_]+');
        });

        Route::controller(IllustratorController::class)->group(function () {
            Route::post('/user/create_illustrator', 'createIllustrator');
            Route::patch('/user/update_illustrator/{slug}', 'updateIllustrator')->where('slug', '[A-Za-z_]+');
            Route::delete('/user/delete_illustrator/{slug}', 'deleteIllustrator')->where('slug', '[A-Za-z_]+');
        });

        Route::controller(PublisherController::class)->group(function () {
            Route::post('/user/create_publisher', 'createPublisher');
            Route::patch('/user/update_publisher/{name}', 'updatePublisher')->whereAlphaNumeric('name');;
            Route::delete('/user/delete_publisher/{name}', 'deletePublisher')->whereAlphaNumeric('name');;
        });

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


// // Search by Book Name
// Route::get('/search/books-by-name', [SearchController::class, 'searchBooksByName']);

// // Search by ISBN
// Route::get('/search/books-by-isbn', [SearchController::class, 'searchBooksByISBN']);

// // Search by Author
// Route::get('/search/books-by-author/{authorName}', [SearchController::class, 'searchBooksByAuthor']);

// // Search by Illustrator
// Route::get('/search/books-by-illustrator/{illustratorName}', [SearchController::class, 'searchBooksByIllustrator']);

// // Search by Publisher
// Route::get('/search/books-by-publisher/{publisherName}', [SearchController::class, 'searchBooksByPublisher']);


// 404
Route::fallback(function () {
    return response()->json(['message' => 'Unbekantes Ziel'], 404);
});
