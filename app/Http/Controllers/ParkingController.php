<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Parking;
use View;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;
use Redirect;

class ParkingController extends Controller
{
    public function index()
    {
        $parkings = Parking::all();
        return View::make('admin.parking', compact('parkings'));
    }

    public function create()
    {
        return View::make('parking.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'plate_number' => 'required|string|max:255',
        ]);

        $parking = new Parking();
        $parking->plate_number = $request->plate_number;
        $parking->parking_start = now(); // Store parking_start as a DateTime object

        $parking->save();
        return redirect()->route('parking.index')->with('success', 'Plate number added successfully.');
    }

    public function update(Request $request, $id)
    {
        $parking = Parking::findOrFail($id);

        if (!$parking->parking_end) {
            $parking->parking_end = now();
            $durationInMinutes = $parking->parking_end->diffInMinutes($parking->parking_start);

            $hourlyRate = 10;
            $charge = ceil($durationInMinutes / 60) * $hourlyRate;
            $parking->charge = max($charge, 10); //

            $parking->save();

            return redirect()->route('parking.index')->with('success', 'Parking marked as done successfully.');
        } else {
            return redirect()->route('parking.index')->with('error', 'Parking has already been marked as done.');
        }
    }

    public function deleteSelected(Request $request)
    {
        try {
            $selectedParkings = $request->input('selectedParkings', []);

            if (!empty($selectedParkings)) {
                Parking::whereIn('plate_number', $selectedParkings)->delete();
                return redirect()->back()->with('success', 'Selected parkings deleted successfully!');
            }

            return redirect()->back()->with('error', 'No parkings selected for deletion.');
        } catch (\Exception $e) {
            \Log::error('Error deleting selected parkings: ' . $e->getMessage());
            return redirect()->back()->with('error', 'An error occurred while deleting selected parkings.');
        }
    }

    public function clear()
    {
        $parkings = Parking::all();
        return view('parking.delete', compact('parkings'));
    }
}
