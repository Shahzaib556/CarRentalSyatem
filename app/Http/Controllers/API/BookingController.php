<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Booking;
use Illuminate\Support\Facades\Auth;

class BookingController extends Controller
{
    // User: Make a booking
    public function store(Request $request)
    {
        $request->validate([
            'car_id' => 'required|exists:cars,id',
            'start_date' => 'required|date|after_or_equal:today',
            'end_date' => 'required|date|after_or_equal:start_date',
        ]);

        $overlap = Booking::where('car_id', $request->car_id)
            ->where('status', '!=', 'cancelled')
            ->where(function ($query) use ($request) {
                $query->whereBetween('start_date', [$request->start_date, $request->end_date])
                      ->orWhereBetween('end_date', [$request->start_date, $request->end_date])
                      ->orWhere(function ($query) use ($request) {
                          $query->where('start_date', '<=', $request->start_date)
                                ->where('end_date', '>=', $request->end_date);
                      });
            })->exists();

        if ($overlap) {
            return response()->json(['message' => 'Car already booked for selected dates'], 409);
        }

        $booking = Booking::create([
            'user_id' => Auth::id(),
            'car_id' => $request->car_id,
            'start_date' => $request->start_date,
            'end_date' => $request->end_date,
            'status' => 'pending',
        ]);

        return response()->json(['message' => 'Booking created', 'booking' => $booking]);
    }

    // Admin: View all bookings
    public function index()
    {
        return Booking::with(['user', 'car'])->get();
    }

    // Admin: Approve booking
    public function approve($id)
    {
        $booking = Booking::findOrFail($id);
        $booking->status = 'approved';
        $booking->save();

        return response()->json(['message' => 'Booking approved', 'booking' => $booking]);
    }

    // Admin/User: Cancel booking
    public function cancel($id)
    {
        $booking = Booking::findOrFail($id);

        // Allow admin OR owner to cancel
        if (Auth::id() !== $booking->user_id && Auth::user()->role !== 'admin') {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $booking->status = 'cancelled';
        $booking->save();

        return response()->json(['message' => 'Booking cancelled', 'booking' => $booking]);
    }

    // Any: Check status of booking
    public function status($id)
    {
        $booking = Booking::with(['user', 'car'])->findOrFail($id);

        // User can only see their own booking unless admin
        if (Auth::id() !== $booking->user_id && Auth::user()->role !== 'admin') {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        return response()->json(['status' => $booking->status, 'booking' => $booking]);
    }

    // User: View own bookings
    public function myBookings()
    {
        return Auth::user()->bookings()->with('car')->get();
    }
}
