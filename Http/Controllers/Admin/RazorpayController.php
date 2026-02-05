<?php

namespace SleepyBear\Razorpay\Http\Controllers\Admin;

use Illuminate\Http\JsonResponse;
use Illuminate\View\View;
use Webkul\Admin\Http\Controllers\Controller;

class RazorpayController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): View
    {
        if (request()->ajax()) {
            // return datagrid(RazorpayDataGrid::class)->process();
        }

        return view('razorpay::admin.index');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        return view('razorpay::admin.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(): JsonResponse
    {
        return new JsonResponse([
            'message' => 'Resource created successfully.',
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(int $id): View
    {
        return view('razorpay::admin.edit', compact('id'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(int $id): JsonResponse
    {
        return new JsonResponse([
            'message' => 'Resource updated successfully.',
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(int $id): JsonResponse
    {
        return new JsonResponse([
            'message' => 'Resource deleted successfully.',
        ]);
    }
}
