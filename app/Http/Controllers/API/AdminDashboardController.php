<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Car;
use App\Models\Booking;
use App\Models\Review;
use App\Models\Query;
use App\Models\Brand;

class AdminDashboardController extends Controller
{
    public function index()
    {
        $stats = [
            'total_users'      => User::count(),
            'total_cars'       => Car::count(),
            'total_brands'     => Brand::count(),
            'total_bookings'   => Booking::count(),
            'total_queries'    => Query::count(),
            'total_reviews'    => Review::count(),
        ];

        return response()->json([
            'success' => true,
            'message' => 'Admin dashboard stats fetched successfully',
            'data'    => $stats
        ]);
    }
}
