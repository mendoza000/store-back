<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\PaymentVerification;
use App\Http\Requests\PaymentVerificationRequest;

class PaymentVerificationController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $paymentVerify = PaymentVerification::all();

        return response()->json([
            'data' => $paymentVerify
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $paymentVerification = PaymentVerification::create($request->validated());

        return response()->json([
            'message' => 'Payment verification created successfully',
            'data' => $paymentVerification
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $paymentVerification = PaymentVerification::find($id);

        if (!$paymentVerification) {
            return response()->json(['message' => 'Payment verification not found'], 404);
        }

        return response()->json(['data' => $paymentVerification]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $paymentVerification = PaymentVerification::find($id);

        if (!$paymentVerification) {
            return response()->json(['message' => 'Payment verification not found'], 404);
        }

        $paymentVerification->update($request->validated());

        return response()->json([
            'message' => 'Payment verification updated successfully',
            'data' => $paymentVerification
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $paymentVerification = PaymentVerification::find($id);

        if (!$paymentVerification) {
            return response()->json(['message' => 'Payment verification not found'], 404);
        }

        $paymentVerification->delete();

        return response()->json(['message' => 'Payment verification deleted successfully']);
    }
}
