<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\AuthController;
use App\Http\Controllers\API\AdminAuthController;
use App\Http\Controllers\API\UserController;
use App\Http\Controllers\API\CarController;
use App\Http\Controllers\API\BookingController;
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

        // Route::post('/made-review', [ReviewController::class, 'store']);

            // Create a new review
        Route::post('/post-review', [ReviewController::class, 'store']);

        //  view all my reviews
        Route::get('/my-reviews', [ReviewController::class, 'myReviews']);

        // Update own review
        Route::put('/update-review/{id}', [ReviewController::class, 'userUpdateMessage']);

        // Delete own review
        Route::delete('/delete-review/{id}', [ReviewController::class, 'destroy']);
        
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

    //------- dashboard routes ------- 
Route::get('/admin-dashboard', [AdminDashboardController::class, 'index']); 

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

    // Show all users
Route::get('/users', [UserController::class, 'listAllUsers']);


    // ------------ Bookings Routes --------- //

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/post-booking', [BookingController::class, 'store']);   // User booking
    Route::get('/my-bookings', [BookingController::class, 'myBookings']); // User's bookings
});
// Route for cheching Car Availability
Route::get('/check-availability/{vehicleId}', [BookingController::class, 'checkAvailability']);



//Routes to be used on admin side
Route::get('/booking-status/{id}/status', [BookingController::class, 'status']); 
Route::put('/cancel-booking/{id}/cancel', [BookingController::class, 'cancel']); 
Route::get('/show-bookings', [BookingController::class, 'index']); // All bookings
Route::put('/approve-booking/{id}/approve', [BookingController::class, 'approve']); // Approve booking
Route::get('/pending-bookings', [BookingController::class, 'pendingBookings']);
Route::get('/canceled-bookings', [BookingController::class, 'cancelledBookings']);
Route::get('/approved-bookings', [BookingController::class, 'approvedBookings']);
// Get a specific booking by ID
Route::get('/show-booking/{id}', [BookingController::class, 'show']);
// Update status of review
Route::put('/update-review/{id}/status', [ReviewController::class, 'adminUpdateStatus']);
// Public & Admin will see whole review details
Route::get('/view-reviews', [ReviewController::class, 'index']);
// Admin can update the paid amount of booking
Route::put('/booking/{id}/update-paid', [BookingController::class, 'updatePaidAmount']);


        // --------review routes ----------//
// Public route to fetch only active reviews
Route::get('/reviews/active', [ReviewController::class, 'activeReviews']);



        //-------- brands routes---------//

Route::get('/show-brands', [BrandController::class, 'index']);
Route::post('/create-brands', [BrandController::class, 'store']);
Route::get('/show-brands/{id}', [BrandController::class, 'show']);
Route::put('/update-brand/{id}', [BrandController::class, 'update']);
Route::delete('/delete-brand/{id}', [BrandController::class, 'destroy']);



    //--------- queries routes -------------//

// Public: anyone can post a query
Route::post('/queries', [QueriesController::class, 'store']);

Route::get('/queries', [QueriesController::class, 'index']);       // list all queries
Route::get('/queries/{id}', [QueriesController::class, 'show']);   // view single query
Route::put('/queries/{id}', [QueriesController::class, 'update']); // update query
Route::delete('/queries/{id}', [QueriesController::class, 'destroy']); // delete query



        // ------contactinfo routes-------

// Get contact info (single record)
Route::get('contactinfo', [ContactInfoController::class, 'index']);

// Update contact info (create if missing)
Route::put('contactinfo', [ContactInfoController::class, 'update']);




