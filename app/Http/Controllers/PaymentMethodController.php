<?php

namespace App\Http\Controllers;

use App\Http\Requests\PaymentMethodRequest;
use App\Models\PaymentMethod;
use Illuminate\Http\Request;

class PaymentMethodController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $methods = PaymentMethod::all();

        return response()->json([
            'data' => $methods
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(PaymentMethodRequest $request)
    {
        $paymentMethod = PaymentMethod::create($request->validated());

        return response()->json([
            'message' => 'Payment method created successfully',
            'data' => $paymentMethod
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $paymentMethod = PaymentMethod::find($id);

        if (!$paymentMethod) {
            return response()->json(['message' => 'Payment method not found'], 404);
        }

        return response()->json(['data' => $paymentMethod]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $paymentMethod = PaymentMethod::find($id);

        if (!$paymentMethod) {
            return response()->json(['message' => 'Payment method not found'], 404);
        }

        $paymentMethod->update($request->all());

        return response()->json([
            'message' => 'Payment method updated successfully',
            'data' => $paymentMethod
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $paymentMethod = PaymentMethod::find($id);

        if (!$paymentMethod) {
            return response()->json(['message' => 'Payment method not found'], 404);
        }

        $paymentMethod->delete();

        return response()->json(['message' => 'Payment method deleted successfully']);
    }
}
