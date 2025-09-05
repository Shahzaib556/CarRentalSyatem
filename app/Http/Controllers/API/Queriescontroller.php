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

    // ✅ Create new record
    public function store(Request $request)
    {
        $request->validate([
            'name'      => 'nullable|string|max:255',
            'Address'   => 'nullable|string',
            'EmailId'   => 'nullable|email|max:255',
            'ContactNo' => 'nullable|string|max:11',
            'message'   => 'nullable|string',
            'status'    => 'nullable|string|in:pending,resolved,closed',
        ]);

        $data = $request->all();
        $data['postingdate']  = Carbon::now();
        $data['updationdate'] = null;
        $data['status']       = $data['status'] ?? 'pending';

        $contact = Query::create($data);

        return response()->json([
            'message' => 'Record created successfully',
            'data'    => $contact
        ], 201);
    }

    // ✅ Update record
    // public function update(Request $request, $id)
    // {
    //     $contact = Query::find($id);
    //     if (!$contact) {
    //         return response()->json(['message' => 'Record not found'], 404);
    //     }

    //     $request->validate([
    //         'name'      => 'nullable|string|max:255',
    //         'Address'   => 'nullable|string',
    //         'EmailId'   => 'nullable|email|max:255',
    //         'ContactNo' => 'nullable|string|max:11',
    //         'message'   => 'nullable|string',
    //         'status'    => 'nullable|string|in:pending,resolved,closed',
    //     ]);

    //     $data = $request->all();
    //     $data['updationdate'] = Carbon::now();

    //     $contact->update($data);

    //     return response()->json([
    //         'message' => 'Record updated successfully',
    //         'data'    => $contact
    //     ], 200);
    // }

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
