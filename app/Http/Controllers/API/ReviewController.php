<?php
// app/Http/Controllers/API/ReviewController.php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Review;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class ReviewController extends Controller
{
    // ✅ Add an overall review
    public function store(Request $request)
    {
        $request->validate([
            'message' => 'required|string',
            'status'  => 'nullable|string|in:pending,approved,rejected',
        ]);

        $review = Review::create([
            'user_id'       => Auth::id(),
            'user_email'    => Auth::user()->email,
            'message'       => $request->message,
            'posting_date'  => Carbon::now(),
            'updation_date' => null,
            'status'        => $request->status ?? 'pending',
        ]);

        return response()->json([
            'message' => 'Review added successfully',
            'review'  => $review
        ], 201);
    }

    // ✅ Update review (only by owner)
    public function update(Request $request, $id)
    {
        $review = Review::findOrFail($id);

        if (Auth::id() !== $review->user_id) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $request->validate([
            'message' => 'nullable|string',
            'status'  => 'nullable|string|in:pending,approved,rejected',
        ]);

        $review->update([
            'message'       => $request->message ?? $review->message,
            'updation_date' => Carbon::now(),
            'status'        => $request->status ?? $review->status,
        ]);

        return response()->json([
            'message' => 'Review updated successfully',
            'review'  => $review
        ]);
    }

    // ✅ Get all reviews (public)
    public function index()
    {
        $reviews = Review::with('user:id,name,email')
            ->orderBy('posting_date', 'desc')
            ->get();

        return response()->json($reviews);
    }

    // ✅ Delete a review (Owner or Admin)
    public function destroy($id)
    {
        $review = Review::findOrFail($id);

        if (Auth::id() !== $review->user_id && !(Auth::user()->is_admin ?? false)) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $review->delete();

        return response()->json(['message' => 'Review deleted successfully']);
    }
}
