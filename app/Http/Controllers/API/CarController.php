<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Car;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class CarController extends Controller
{
    // Get all cars with brand details
    public function index()
    {
        $cars = Car::with('brand')->get();

        return response()->json([
            'success' => true,
            'data' => $cars
        ]);
    }

    // Store new car
    public function store(Request $request)
{
    $data = $request->validate([
        'CarTitle' => 'required|string|max:150',
        'CarBrand' => 'nullable|integer|exists:tblbrands,id',
        'CarOverview' => 'nullable|string',
        'PricePerDay' => 'required|integer',
        'FuelType' => 'nullable|string|max:100',
        'ModelYear' => 'nullable|integer',
        'SeatingCapacity' => 'nullable|integer',
        'Image1' => 'nullable|image|mimes:jpg,jpeg,png|max:7048',
        'Image2' => 'nullable|image|mimes:jpg,jpeg,png|max:7048',
        'Image3' => 'nullable|image|mimes:jpg,jpeg,png|max:7048',
        'Image4' => 'nullable|image|mimes:jpg,jpeg,png|max:7048',
        'Image5' => 'nullable|image|mimes:jpg,jpeg,png|max:7048',
        'AirConditioner' => 'nullable|boolean',
        'PowerDoorLocks' => 'nullable|boolean',
        'AntiLockBrakingSystem' => 'nullable|boolean',
        'BrakeAssist' => 'nullable|boolean',
        'PowerSteering' => 'nullable|boolean',
        'DriverAirbag' => 'nullable|boolean',
        'PassengerAirbag' => 'nullable|boolean',
        'PowerWindows' => 'nullable|boolean',
        'CDPlayer' => 'nullable|boolean',
        'CentralLocking' => 'nullable|boolean',
        'CrashSensor' => 'nullable|boolean',
        'LeatherSeats' => 'nullable|boolean',
    ]);

    // Handle image uploads
    $imageFields = ['Image1', 'Image2', 'Image3', 'Image4', 'Image5'];
    foreach ($imageFields as $field) {
        if ($request->hasFile($field)) {
            $data[$field] = $request->file($field)->store('cars', 'public');
        }
    }

    $car = Car::create($data);

    return response()->json([
        'success' => true,
        'message' => 'Car created successfully',
        'data' => $car->load('brand')
    ], 201);
}

    // Show single car
    public function show($id)
    {
        $car = Car::with('brand')->find($id);

        if (!$car) {
            return response()->json([
                'success' => false,
                'message' => 'Car not found'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $car
        ]);
    }

    // Update car
    public function update(Request $request, $id)
    {
        $car = Car::find($id);

        if (!$car) {
            return response()->json([
                'success' => false,
                'message' => 'Car not found'
            ], 404);
        }

        $data = $request->validate([
            'CarTitle' => 'sometimes|string|max:150',
            'CarBrand' => 'sometimes|integer|exists:tblbrands,id',
            'CarOverview' => 'sometimes|string',
            'PricePerDay' => 'sometimes|integer',
            'FuelType' => 'sometimes|string|max:100',
            'ModelYear' => 'sometimes|integer',
            'SeatingCapacity' => 'sometimes|integer',
            'Image1' => 'nullable|image|mimes:jpg,jpeg,png|max:7048',
        ]);

        if ($request->hasFile('Image1')) {
            if ($car->Image1) {
                Storage::disk('public')->delete($car->Image1);
            }
            $data['Image1'] = $request->file('Image1')->store('cars', 'public');
        }

        $car->update($data);

        return response()->json([
            'success' => true,
            'message' => 'Car updated successfully',
            'data' => $car->load('brand')
        ]);
    }

    // Delete car
    public function destroy($id)
    {
        $car = Car::find($id);

        if (!$car) {
            return response()->json([
                'success' => false,
                'message' => 'Car not found'
            ], 404);
        }

        if ($car->Image1) {
            Storage::disk('public')->delete($car->Image1);
        }

        $car->delete();

        return response()->json([
            'success' => true,
            'message' => 'Car deleted successfully'
        ]);
    }
}
