<?php

// app/Http/Controllers/API/ReviewController.php
namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Review;
use App\Models\Booking;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ReviewController extends Controller
{
    // Add a review
    public function store(Request $request)
    {
        $request->validate([
            'car_id' => 'required|exists:cars,id',
            'rating' => 'required|integer|min:1|max:5',
            'comment' => 'nullable|string',
        ]);

        // ✅ Ensure user has rented this car before reviewing
        $hasRented = Booking::where('user_id', Auth::id())
            ->where('car_id', $request->car_id)
            ->where('status', 'Approved') // only after approved rentals
            ->exists();

        if (!$hasRented) {
            return response()->json(['error' => 'You can only review cars you have rented.'], 403);
        }

        $review = Review::create([
            'user_id' => Auth::id(),
            'car_id' => $request->car_id,
            'rating' => $request->rating,
            'comment' => $request->comment,
        ]);

        return response()->json([
            'message' => 'Review added successfully',
            'review' => $review
        ], 201);
    }

    // Get reviews for a car
    public function carReviews($carId)
    {
        $reviews = Review::where('car_id', $carId)
            ->with('user:id,name')
            ->latest()
            ->get();

        return response()->json($reviews);
    }

    // Optional: Delete review (Admin or Owner)
    public function destroy($id)
    {
        $review = Review::findOrFail($id);

        if (Auth::id() !== $review->user_id && !Auth::user()->is_admin) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $review->delete();

        return response()->json(['message' => 'Review deleted successfully']);
    }
}
