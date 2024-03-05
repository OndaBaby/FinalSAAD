<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Payment;
use App\Models\Tenant;
use App\Models\User;
use View;
use Illuminate\Support\Facades\Log;

class PaymentController extends Controller
{
    public function index()
    {
        $users = User::with('tenant')->get();
        $tenants = Tenant::with('payment')->get();

        $payments = Payment::all();
        return View::make('admin.payment', compact('payments'));
    }

    public function edit(string $id)
    {
        $payment = Payment::findOrFail($id);
        return view('payment.edit', compact('payment'));
    }

    public function update(Request $request, $id)
    {
        try {
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
        } catch (\Exception $e) {
            Log::error('Error updating payment: ' . $e->getMessage());
            return back()->withInput()->with('error', 'An error occurred while updating the payment.');
        }
    }
}
