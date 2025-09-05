<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\AuthController;
use App\Http\Controllers\API\AdminAuthController;
use App\Http\Controllers\API\UserController;
use App\Http\Controllers\API\CarController;
use App\Http\Controllers\BookingController;
use App\Http\Controllers\API\QueriesController;
use App\Http\Controllers\API\ReviewController;
use App\Http\Controllers\API\AdminDashboardController;
use App\Http\Controllers\API\BrandController;
use App\Http\Controllers\API\ContactInfoController;

/*------------------------------------------
| Public User Routes
|------------------------------------------*/
Route::prefix('auth')->group(function () {
    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/login', [AuthController::class, 'login']);
    Route::post('/forgot-password', [AuthController::class, 'forgotPassword']);
    Route::post('/reset-password', [AuthController::class, 'resetPassword']);
});



/*------------------------------------------
| Authenticated Public User Routes
|------------------------------------------*/
Route::middleware(['auth:sanctum'])->group(function () {
    // User profile routes
    Route::prefix('profile')->group(function () {
        
        Route::get('/profileconfig', [UserController::class, 'profile']);
        Route::put('/profileconfig', [UserController::class, 'updateProfile']);
        
        Route::get('/get-password', [UserController::class, 'getUserPassword']);
        Route::post('/update-password', [UserController::class, 'updateUserPassword']);

        Route::post('/document', [UserController::class, 'uploadDocument']);

        Route::get('/my-bookings', [BookingController::class, 'myBookings']);

        Route::post('/made-review', [ReviewController::class, 'store']);
    });

    // Auth logout route for both Admin and User
    Route::post('auth/logout', [AuthController::class, 'logout']);

    // // Public route to see reviews of a specific car
    // Route::get('/cars/{id}/reviews', [ReviewController::class, 'carReviews']);

});



/*------------------------------------------
| Public Admin Routes
|------------------------------------------*/
Route::prefix('admin/auth')->group(function () {
    // Public login route
    Route::post('/login', [AdminAuthController::class, 'login']);
    
    // Protected route for update , accessible when admin is logged in already
    Route::middleware(['auth:sanctum'])->group(function () {
        Route::post('/update-password', [AdminAuthController::class, 'updatePassword']);
    });
});



/*------------------------------------------
| Authenticated Admin Routes
|------------------------------------------*/
    // Admin dashboard
    // Route::get('/admin/dashboard', fn() => response()->json(['message' => 'Welcome Admin']));

    // Routes for admins only
    // Create new car
    Route::post('/post-car', [CarController::class, 'store']);

    // List all cars
    Route::get('/show-cars', [CarController::class, 'index']);

    // View single car
    Route::get('/show-car/{id}', [CarController::class, 'show']);

    // Update existing car
      Route::put('/update-car/{id}', [CarController::class, 'update']);

    // Delete car
      Route::delete('/delete-car/{id}', [CarController::class, 'destroy']);
    // });


    // Bookings Routes
    Route::middleware('auth:sanctum')->group(function () {
    Route::post('/bookings', [BookingController::class, 'store']);   // User booking
    Route::get('/my-bookings', [BookingController::class, 'myBookings']); // User's bookings
    Route::get('/bookings/{id}/status', [BookingController::class, 'status']); // Booking status
    Route::put('/bookings/{id}/cancel', [BookingController::class, 'cancel']); // Cancel booking

    // Admin only
    Route::middleware('admin')->group(function () {
        Route::get('/bookings', [BookingController::class, 'index']); // All bookings
        Route::put('/bookings/{id}/approve', [BookingController::class, 'approve']); // Approve booking
    });
});


    // review routes
    // use App\Http\Controllers\API\ReviewController;

// Grouped under auth:sanctum → only logged-in users can add/update/delete reviews
Route::middleware('auth:sanctum')->group(function () {
    // Create a new review
    Route::post('/reviews', [ReviewController::class, 'store']);

    // Update own review
    Route::put('/reviews/{id}', [ReviewController::class, 'update']);

    // Delete own review
    Route::delete('/reviews/{id}', [ReviewController::class, 'destroy']);
});

// Public routes → anyone can see reviews
Route::get('/reviews', [ReviewController::class, 'index']);




// brands routes
    Route::get('/show-brands', [BrandController::class, 'index']);
    Route::post('/create-brands', [BrandController::class, 'store']);
    Route::get('/show-brands/{id}', [BrandController::class, 'show']);
    Route::put('/update-brand/{id}', [BrandController::class, 'update']);
    Route::delete('/delete-brand/{id}', [BrandController::class, 'destroy']);


// queries routes

// Public: anyone can post a query
Route::post('/queries', [QueriesController::class, 'store']);

// Admin/User: require authentication for managing queries
Route::middleware('auth:sanctum')->group(function () {
    Route::get('/queries', [QueriesController::class, 'index']);       // list all queries
    Route::get('/queries/{id}', [QueriesController::class, 'show']);   // view single query
    // Route::put('/queries/{id}', [QueriesController::class, 'update']); // update query
    Route::delete('/queries/{id}', [QueriesController::class, 'destroy']); // delete query
});



// dashboard routes
Route::get('/admin/dashboard', [AdminDashboardController::class, 'index']); //not working


// contactinfo routes
Route::apiResource('contactinfo', ContactInfoController::class);



