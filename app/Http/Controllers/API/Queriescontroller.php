<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\ContactInfo;
use Illuminate\Http\Request;

class QueriesController extends Controller
{
    // ✅ Get all records
    public function index()
    {
        return response()->json(ContactInfo::all(), 200);
    }

    // ✅ Get single record by ID
    public function show($id)
    {
        $contact = ContactInfo::find($id);
        if (!$contact) {
            return response()->json(['message' => 'Record not found'], 404);
        }
        return response()->json($contact, 200);
    }

    // ✅ Create new record
    public function store(Request $request)
    {
        $request->validate([
            'Address' => 'nullable|string',
            'EmailId' => 'nullable|email|max:255',
            'ContactNo' => 'nullable|string|max:11',
        ]);

        $contact = ContactInfo::create($request->all());

        return response()->json([
            'message' => 'Record created successfully',
            'data' => $contact
        ], 201);
    }

    // ✅ Update record
    public function update(Request $request, $id)
    {
        $contact = ContactInfo::find($id);
        if (!$contact) {
            return response()->json(['message' => 'Record not found'], 404);
        }

        $request->validate([
            'Address' => 'nullable|string',
            'EmailId' => 'nullable|email|max:255',
            'ContactNo' => 'nullable|string|max:11',
        ]);

        $contact->update($request->all());

        return response()->json([
            'message' => 'Record updated successfully',
            'data' => $contact
        ], 200);
    }

    // ✅ Delete record
    public function destroy($id)
    {
        $contact = ContactInfo::find($id);
        if (!$contact) {
            return response()->json(['message' => 'Record not found'], 404);
        }

        $contact->delete();

        return response()->json(['message' => 'Record deleted successfully'], 200);
    }
}
