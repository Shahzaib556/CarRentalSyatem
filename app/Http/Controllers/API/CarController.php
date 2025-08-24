<?php


namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Car;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class CarController extends Controller
{
    // Get all cars
    public function index()
    {
        return response()->json(Car::all());
    }

    // Store new car
    public function store(Request $request)
    {
        $data = $request->validate([
            'CarTitle' => 'required|string|max:150',
            'CarBrand' => 'nullable|integer',
            'CarOverview' => 'nullable|string',
            'PricePerDay' => 'required|integer',
            'FuelType' => 'nullable|string|max:100',
            'ModelYear' => 'nullable|integer',
            'SeatingCapacity' => 'nullable|integer',
            'Image1' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
        ]);

        if ($request->hasFile('Image1')) {
            $data['Image1'] = $request->file('Image1')->store('cars', 'public');
        }

        $car = Car::create($data);

        return response()->json(['message' => 'Car created successfully', 'car' => $car]);
    }

    // Show single car
    public function show($id)
    {
        return response()->json(Car::findOrFail($id));
    }

    // Update car
    public function update(Request $request, $id)
    {
        $car = Car::findOrFail($id);

        $data = $request->validate([
            'CarTitle' => 'sometimes|string|max:150',
            'PricePerDay' => 'sometimes|integer',
            'FuelType' => 'sometimes|string|max:100',
            'ModelYear' => 'sometimes|integer',
            'SeatingCapacity' => 'sometimes|integer',
            'Image1' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
        ]);

        if ($request->hasFile('Image1')) {
            if ($car->Image1) {
                Storage::disk('public')->delete($car->Image1);
            }
            $data['Image1'] = $request->file('Image1')->store('cars', 'public');
        }

        $car->update($data);

        return response()->json(['message' => 'Car updated successfully', 'car' => $car]);
    }

    // Delete car
    public function destroy($id)
    {
        $car = Car::findOrFail($id);

        if ($car->Image1) {
            Storage::disk('public')->delete($car->Image1);
        }

        $car->delete();

        return response()->json(['message' => 'Car deleted successfully']);
    }
}
