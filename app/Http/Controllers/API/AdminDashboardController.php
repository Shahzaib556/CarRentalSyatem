<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Car;
use App\Models\Booking;
use App\Models\Review;
use App\Models\Queries;       // queries table (renamed from Contactusinfo)
use App\Models\Brand;
// use App\Models\Payment;
use App\Models\ContactInfo;   // contact info table (map location, etc.)

class AdminDashboardController extends Controller
{
    public function index()
    {
        $stats = [
            'total_users'      => User::count(),
            'total_cars'       => Car::count(),
            'total_brands'     => Brand::count(),
            'total_bookings'   => Booking::count(),
            'total_queries'    => Queries::count(),
            'total_reviews'    => Review::count(),
            // 'total_payments'   => Payment::count(),
            'total_contacts'   => ContactInfo::count(),
            'total_revenue'    => Payment::sum('amount'), // if amount field exists in payments
            'latest_bookings'  => Booking::with(['user:id,name,email', 'car:id,VehiclesTitle'])
                                        ->latest()
                                        ->take(5)
                                        ->get(),
        ];

        return response()->json([
            'success' => true,
            'message' => 'Admin dashboard stats fetched successfully',
            'data'    => $stats
        ]);
    }
}
