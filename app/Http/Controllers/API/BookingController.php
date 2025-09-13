<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Booking;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class BookingController extends Controller
{
    // User: Make a booking
    public function store(Request $request)
{
    $request->validate([
        'car_id'     => 'required|exists:tblcars,id',
        'start_date' => 'required|date|after_or_equal:today',
        'end_date'   => 'required|date|after_or_equal:start_date',
        'message'    => 'nullable|string',
        'receipt'    => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:7200',
        // 'receipt' => 'nullable|string',
    ]);

    // Prevent overlapping bookings
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

    // Handle receipt upload
    $receiptPath = null;
    if ($request->hasFile('receipt')) {
        $receiptPath = $request->file('receipt')->store('receipts', 'public');
    }

    $booking = Booking::create([
        'booking_no' => 'BK-' . strtoupper(Str::random(8)),
        'user_id'    => Auth::id(),
        'useremail'  => Auth::user()->email,
        'car_id'     => $request->car_id,
        'start_date' => $request->start_date,
        'end_date'   => $request->end_date,
        'message'    => $request->message,
        'receipt'    => $receiptPath,
        'status'     => 'pending',
        'paid_amount'=> 0,
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
    $booking->save(); // will auto-update 'updated_at'

    return response()->json([
        'message' => 'Booking approved',
        'booking' => $booking
    ]);
}

    // Cancel booking
// Cancel booking (admin only usage)
public function cancel($id)
{
    $booking = Booking::findOrFail($id);

    // Only allow cancelling if booking is still pending
    if ($booking->status !== 'pending') {
        return response()->json(['message' => 'Only pending bookings can be cancelled'], 400);
    }

    $booking->status = 'cancelled';
    $booking->save(); // updates status and updated_at automatically

    return response()->json([
        'message' => 'Booking cancelled',
        'booking' => $booking
    ]);
}



    // Any: Check status of booking
    public function status($id)
    {
        $booking = Booking::with(['user', 'car'])->findOrFail($id);

        // User can only see their own booking unless admin
        if (Auth::id() !== $booking->user_id && Auth::user()->role !== 'admin') {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        return response()->json([
            'status'  => $booking->status,
            'booking' => $booking
        ]);
    }

    // User: View own bookings
    public function myBookings()
    {
        return Auth::user()->bookings()->with('car')->get();
    }

    // inside BookingController.php

// Admin: View only pending bookings
public function pendingBookings()
{
    $bookings = Booking::with(['user', 'car'])
        ->where('status', 'pending')
        ->get();

    return response()->json([
        'status' => 'success',
        'bookings' => $bookings
    ]);
}

// Admin: View only cancelled bookings
public function cancelledBookings()
{
    $bookings = Booking::with(['user', 'car'])
        ->where('status', 'cancelled')
        ->get();

    return response()->json([
        'status' => 'success',
        'bookings' => $bookings
    ]);
}

// Admin: View only approved bookings
    public function approvedBookings()
    {
        $bookings = Booking::with(['user', 'car'])
            ->where('status', 'approved')
            ->get();

        return response()->json([
            'status' => 'success',
            'bookings' => $bookings
        ]);
    }

    // Get full details of a specific booking by ID
    public function show($id)
    {
        // Fetch the booking with related user and car
        $booking = Booking::with(['user', 'car'])->find($id);

        if (!$booking) {
            return response()->json([
                'status' => 'error',
                'message' => 'Booking not found'
            ], 404);
        }

        return response()->json([
            'status' => 'success',
            'booking' => $booking
        ]);
    }

    // Admin: Update paid amount
    public function updatePaidAmount(Request $request, $id)
    {
        $request->validate([
            'paid_amount' => 'required|numeric|min:0'
        ]);

        $booking = Booking::findOrFail($id);
        $booking->paid_amount = $request->paid_amount;
        $booking->save();

        return response()->json([
            'message' => 'Paid amount updated'
        ]);
    }

public function checkAvailability(Request $request, $carId)
{
    if (!$request->has('start_date') || !$request->has('end_date')) {
        return response()->json([
            'available' => false,
            'message'   => 'Start and end date are required'
        ], 400);
    }

    $startDate = \Carbon\Carbon::parse($request->query('start_date'))->startOfDay();
    $endDate   = \Carbon\Carbon::parse($request->query('end_date'))->endOfDay();

    $conflict = Booking::where('car_id', $carId)
        ->whereIn('status', ['pending', 'approved'])
        ->where(function ($query) use ($startDate, $endDate) {
            $query->where(function ($q) use ($startDate, $endDate) {
                // 🔴 If requested period overlaps with existing booking
                $q->where('start_date', '<=', $endDate)
                  ->where('end_date', '>=', $startDate);
            });
        })
        ->first();

    if ($conflict) {
        return response()->json([
            'available'    => false,
            'message'      => "Car is already booked from {$conflict->start_date} to {$conflict->end_date}",
            'booked_until' => $conflict->end_date
        ], 409);
    }

    return response()->json([
        'available' => true,
        'message'   => 'Car is available for the selected dates'
    ]);
}





}
