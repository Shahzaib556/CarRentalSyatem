<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Query;
use Illuminate\Http\Request;
use Carbon\Carbon;

class QueriesController extends Controller
{
    // ✅ Get all records
    public function index()
    {
        return response()->json(Query::all(), 200);
    }

    // ✅ Get single record by ID
    public function show($id)
    {
        $contact = Query::find($id);
        if (!$contact) {
            return response()->json(['message' => 'Record not found'], 404);
        }
        return response()->json($contact, 200);
    }

        // ✅ Create new record (user submission)
        public function store(Request $request)
{
  
    $request->validate([
    'name'      => 'required|string|max:255',
    'EmailId'   => 'required|email|max:255',
    'ContactNo' => 'required|string|max:11',
    'message'   => 'required|string|max:255',
    ]);

    // Prepare data to insert
    $data = $request->only(['name', 'EmailId', 'ContactNo', 'message']);
    $data['status'] = 'pending';           // default status
    $data['posting_date'] = now();         // current timestamp

    // Insert into DB
    $query = Query::create($data);

    return response()->json([
        'message' => 'Query submitted successfully',
        'data'    => $query
    ], 201);
}




    // ✅ Update record
   // Update record (mark as resolved)
    public function update(Request $request, $id)
    {
        $contact = Query::find($id);
        if (!$contact) {
            return response()->json(['message' => 'Record not found'], 404);
        }

        $request->validate([
            'status' => 'required|string|in:pending,resolved', // only these statuses
        ]);

        $contact->status = $request->status;
        $contact->save();

        return response()->json([
            'message' => 'Query status updated successfully',
            'data'    => $contact
        ], 200);
    }


    // ✅ Delete record
    public function destroy($id)
    {
        $contact = Query::find($id);
        if (!$contact) {
            return response()->json(['message' => 'Record not found'], 404);
        }

        $contact->delete();

        return response()->json(['message' => 'Record deleted successfully'], 200);
    }
}
