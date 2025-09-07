<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\ContactInfo;
use Illuminate\Http\Request;

class ContactInfoController extends Controller
{
    // ✅ Get all contact info
    public function index()
{
    $contact = ContactInfo::first();

    if (!$contact) {
        return response()->json([
            'message' => 'No contact info found'
        ], 404);
    }

    return response()->json($contact, 200);
}

    // ✅ Get single record
    public function show($id)
    {
        $contact = ContactInfo::find($id);
        if (!$contact) {
            return response()->json(['message' => 'Record not found'], 404);
        }
        return response()->json($contact, 200);
    }

    // ✅ Store new contact info
    public function store(Request $request)
    {
        $request->validate([
            'address'      => 'required|string|max:255',
            'map_location' => 'required|string|max:255',
            'emailid'      => 'required|email|max:255',
            'contactno'    => 'required|string|max:11',
        ]);

        $contact = ContactInfo::create($request->all());

        return response()->json([
            'message' => 'Contact info created successfully',
            'data'    => $contact
        ], 201);
    }

    // ✅ Update contact info (for only one record in table max)
public function update(Request $request)
{
    $request->validate([
        'address'      => 'required|string|max:255',
        'map_location' => 'required|string|max:255',
        'emailid'      => 'required|email|max:255',
        'contactno'    => 'required|string|max:20',
    ]);

    // Get the first record
    $contact = ContactInfo::first();

    if (!$contact) {
        // No record exists → create first
        $contact = ContactInfo::create($request->all());
    } else {
        // Update existing
        $contact->update($request->all());
    }

    return response()->json([
        'message' => 'Contact info updated successfully',
        'data'    => $contact
    ], 200);
}



    // ✅ Delete contact info
    public function destroy($id)
    {
        $contact = ContactInfo::find($id);
        if (!$contact) {
            return response()->json(['message' => 'Record not found'], 404);
        }

        $contact->delete();

        return response()->json(['message' => 'Contact info deleted successfully'], 200);
    }
}
