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
    // Add an overall review
    public function store(Request $request)
    {
        $request->validate([
            'message' => 'required|string',
            'status'  => 'nullable|string|in:inactive,active',
        ]);

        $review = Review::create([
            'user_id'       => Auth::id(),
            'user_email'    => Auth::user()->email,
            'message'       => $request->message,
            'posting_date'  => Carbon::now(),
            'updation_date' => null,
            'status'        => $request->status ?? 'inactive',
        ]);

        return response()->json([
            'message' => 'Review added successfully',
            'review'  => $review
        ], 201);
    }


//  Admin can update status only using this method
    public function adminUpdateStatus($id)
    {
        $review = Review::findOrFail($id);

        // Toggle status only
        $newStatus = $review->status === 'active' ? 'inactive' : 'active';

        $review->update([
            'status' => $newStatus,
        ]);

        return response()->json([
            'message' => "Review status changed to {$newStatus}",
            'review'  => $review
        ]);
    }


//  User can update only their message using this method
public function userUpdateReview(Request $request, $id)
{
    $request->validate([
        'message' => 'required|string|max:1000',
    ]);

    $review = Review::findOrFail($id);

    
    $review->update([
        'message' => $request->message,
        'status' => 'inactive',      
        'updation_date' => now(),    
    ]);

    return response()->json([
        'message' => 'Review updated successfully and sent for approval again.',
        'review'  => $review
    ]);
}


    // Get all reviews (public)
    public function index()
    {
        $reviews = Review::with('user:id,name,email,profile_image')
            ->orderBy('posting_date', 'desc')
            ->get();

        return response()->json($reviews);
    }


// Fetch reviews of the logged-in user only
        public function myReviews()
    {
        $userId = Auth::id();

        $reviews = Review::where('user_id', $userId)
            ->orderBy('posting_date', 'desc')
            ->get(['id', 'message', 'status', 'posting_date']); 

        return response()->json($reviews);
    }

    // Get only active reviews with user details
    public function activeReviews()
    {
        $reviews = Review::where('status', 'active')
            ->with('user:id,name,profile_image') 
            ->orderBy('posting_date', 'desc')
            ->get(['id', 'user_id', 'message', 'posting_date']);

        // Format response to include only required fields
        $formatted = $reviews->map(function ($review) {
            return [  
                'profile_image' => $review->user->profile_image,            
                'message'       => $review->message,
                'name'          => $review->user->name,
                'posting_date'  => $review->posting_date,
            ];
        });

        return response()->json($formatted);
    }

    
    // Delete a review (only by user who write it)
    public function destroy($id)
    {
        $review = Review::findOrFail($id);

        if (Auth::id() !== $review->user_id) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $review->delete();

        return response()->json(['message' => 'Review deleted successfully']);
    }

}
