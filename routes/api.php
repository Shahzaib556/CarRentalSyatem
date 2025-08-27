<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\AuthController;
use App\Http\Controllers\API\AdminAuthController;
use App\Http\Controllers\API\UserController;
use App\Http\Controllers\API\CarController;
use App\Http\Controllers\BookingController;


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
| Public Admin Routes
|------------------------------------------*/
Route::prefix('admin/auth')->group(function () {
    // Public login route
    Route::post('/login', [AdminAuthController::class, 'login']);
    
    // Protected routes
    Route::middleware(['auth:sanctum'])->group(function () {
        Route::post('/update-password', [AdminAuthController::class, 'updatePassword']);
    });
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
    });

    // Auth routes
    Route::post('auth/logout', [AuthController::class, 'logout']);

    // Routes for authenticated users (customers)
    Route::middleware(['auth:sanctum'])->group(function () {
      // List all cars
      Route::get('/cars', [CarController::class, 'index']);

      // View single car
      Route::get('/cars/{id}', [CarController::class, 'show']);
   });
});

/*------------------------------------------
| Authenticated Admin Routes
|------------------------------------------*/
Route::middleware(['auth:sanctum', 'ability:admin:access'])->group(function () {
    // Admin dashboard
    Route::get('/admin/dashboard', fn() => response()->json(['message' => 'Welcome Admin']));

    // Routes for admins only
     Route::middleware(['auth:sanctum', 'ability:admin:access'])->group(function () {
    // Create new car
      Route::post('/cars', [CarController::class, 'store']);

    // Update existing car
      Route::put('/cars/{id}', [CarController::class, 'update']);

    // Delete car
      Route::delete('/cars/{id}', [CarController::class, 'destroy']);
    });

    // Add other admin-only routes here
});

// use App\Http\Controllers\API\BookingController;
// booking routes
Route::middleware(['auth:sanctum'])->group(function () {
    // User actions
    Route::post('/bookings', [BookingController::class, 'store']);
    Route::get('/my-bookings', [BookingController::class, 'myBookings']);
    Route::put('/bookings/{id}/cancel', [BookingController::class, 'cancel']);
    Route::get('/bookings/{id}/status', [BookingController::class, 'status']);

    // Admin only
    Route::middleware('is_admin')->group(function () {
        Route::get('/bookings', [BookingController::class, 'index']);
        Route::put('/bookings/{id}/approve', [BookingController::class, 'approve']);
    });
});
// review routes
use App\Http\Controllers\API\ReviewController;

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/reviews', [ReviewController::class, 'store']);
    Route::get('/reviews', [ReviewController::class, 'index']);   // ✅ show all reviews
    Route::delete('/reviews/{id}', [ReviewController::class, 'destroy']); // ✅ delete review
});

// Public route to see reviews of a specific car
Route::get('/cars/{id}/reviews', [ReviewController::class, 'carReviews']);


// queries routes
use App\Http\Controllers\API\QueriesController;

Route::apiResource('contact-info', QueriesController::class);

// dashboard routes

use App\Http\Controllers\API\AdminDashboardController;

Route::get('/admin/dashboard', [AdminDashboardController::class, 'index']);


// brands routes

use App\Http\Controllers\API\BrandController;

Route::middleware(['auth:sanctum'])->group(function () {
    Route::get('/brands', [BrandController::class, 'index']);
    Route::post('/brands', [BrandController::class, 'store']);
    Route::get('/brands/{id}', [BrandController::class, 'show']);
    Route::put('/brands/{id}', [BrandController::class, 'update']);
    Route::delete('/brands/{id}', [BrandController::class, 'destroy']);
});
