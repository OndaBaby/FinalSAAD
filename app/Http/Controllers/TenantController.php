<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Tenant;
use App\Models\User;
use App\Models\Stall;
use App\Models\Payment;
use Illuminate\Support\Facades\Auth;
use View;
use Storage;

class TenantController extends Controller
{
    public function index()
    {
        $tenants = Tenant::with('user', 'stalls')->get();
        $users = User::with('tenant')->get();

        return View::make('admin.tenant', compact('tenants', 'users'));
    }

    public function create(Request $request)
    {
        $stallId = $request->query('id');
        return view('tenant.create', compact('stallId'));
    }

    public function store(Request $request, $stallId)
    {
         $request->validate([
             'age' => 'required',
             'contact_no' => 'required',
             'address' => 'required',
             'leaseagreement' => 'required|file|mimes:pdf|max:2048',
             'img_path' => 'required|file|image|max:2048',
         ]);

         if (!$user = Auth::user()) {
             return redirect()->back()->with('error', 'User not authenticated.');
         }

         $stall = Stall::find($stallId);

         if (!$stall) {
             return redirect()->back()->with('error', 'Stall not found.');
         }

         if ($stall->status === 'Rented') {
             return redirect()->back()->with('error', 'Stall is already rented.');
         } elseif ($stall->status === 'Maintenance') {
             return redirect()->back()->with('error', 'Stall is under maintenance.');
         }

         $leaseAgreementPath = $request->file('leaseagreement')->storeAs('public/leaseagreement', $request->file('leaseagreement')->getClientOriginalName());

         $imagePath = $request->file('img_path')->storeAs('public/images', $request->file('img_path')->getClientOriginalName());

         $tenant = new Tenant([
             'age' => $request->input('age'),
             'contact_no' => $request->input('contact_no'),
             'address' => $request->input('address'),
             'leaseagreement' => str_replace('public/', '', $leaseAgreementPath),
             'img_path' => str_replace('public/', '', $imagePath),
         ]);

         $user->tenant()->save($tenant);

         $stall->tenants()->attach($tenant->id);

         $stall->status = 'Rented';
         $stall->save();

         $rentalRate = $stall->rental_rate;
         $amountToBePaid = $rentalRate;
         $balance = $rentalRate;

         $payment = new Payment([
             'amount_to_be_paid' => $amountToBePaid,
             'amount_paid' => 0,
             'balance' => $balance,
             'remarks' => 'none',
         ]);

         $tenant->payment()->save($payment);

         return redirect()->back()->with('success', 'Tenant application submitted successfully!');
     }

    public function edit($id)
    {
        $tenant = Tenant::findOrFail($id);
        return view('tenant.edit', compact('tenant'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'rental_start' => 'required|date',
            'rental_end' => 'required|date',
        ]);

        $tenant = Tenant::findOrFail($id);
        $tenant->rental_start = $request->rental_start;
        $tenant->rental_end = $request->rental_end;
        $tenant->save();

        return redirect()->back()->with('success', 'Rental details updated successfully.');
    }
}
