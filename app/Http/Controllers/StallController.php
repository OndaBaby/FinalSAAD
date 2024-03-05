<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use Storage;
use View;
use Validator;
use App\Models\Stall;
use Redirect;

class StallController extends Controller
{
    public function index()
    {
        $stalls = Stall::all();
        return View::make('admin.stall', compact('stalls'));
    }

    public function create()
    {
        return View::make('stall.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'codename' => 'required|string',
            'description' => 'required|string',
            'status' => 'required|string',
            'rental_rate' => 'required|numeric',
            'img_path.*' => 'required|image|mimes:jpg,bmp,png|max:2048', // Adjust max file size as needed
        ]);

        $stall = new Stall();
        $stall->codename = $request->codename;
        $stall->description = $request->description;
        $stall->status = $request->status;
        $stall->rental_rate = $request->rental_rate;

        if ($request->hasFile('img_path')) {
            foreach ($request->file('img_path') as $image) {
                $path = $image->store('public/images');
                $stall->img_path = str_replace('public/', 'storage/', $path);
            }
        }

        $stall->save();

        return redirect()->route('stall.index')->with('success', 'Stall created successfully.');
    }

    public function edit(string $id)
    {
        $stall = Stall::find($id);
        return View::make('stall.edit', compact('stall'));
    }

    public function update(Request $request, $id)
    {
        $payment = Payment::findOrFail($id);

        $data = [
            'amount_paid' => $request->amount_paid,
            'remarks' => $request->remarks,
        ];

        if ($request->has('amount_paid')) {
            $previous_amount_paid = $payment->amount_paid;
            $data['amount_to_be_paid'] = $payment->amount_to_be_paid - ($request->amount_paid - $previous_amount_paid);
            $data['balance'] = $data['amount_to_be_paid'] - $request->amount_paid;
        }

        $payment->update($data);

        return redirect()->route('payment.index')->with('success', 'Payment updated successfully');
    }

    public function destroy(string $id)
    {
        Stall::destroy($id);
        return Redirect::to('stall');
    }
}
