<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class CustomerController extends Controller
{
    /**
     * READ: Get all customers (with search & pagination)
     * GET /api/customers
     */
    public function index(Request $request): JsonResponse
    {
        $query = Customer::query();
        // 1. GLOBAL SEARCH (name, email, phone, address)
        if ($request->filled('search')) {
            $search = trim($request->input('search'));
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                ->orWhere('email', 'like', "%{$search}%")
                ->orWhere('phone', 'like', "%{$search}%")
                ->orWhere('address', 'like', "%{$search}%");
            });
        }
        // 2. SPECIFIC COLUMN FILTERS (Optional exact or partial matching)
        if ($request->filled('email')) {
            $query->where('email', 'like', '%' . $request->input('email') . '%');
        }
        if ($request->filled('phone')) {
            $query->where('phone', 'like', '%' . $request->input('phone') . '%');
        }
        // 3. SAFE SORTING (Whitelist allowed columns to prevent SQL errors)
        $allowedSortColumns = ['id', 'name', 'email', 'phone', 'created_at'];
        
        $sortBy    = in_array($request->input('sort_by'), $allowedSortColumns) 
                    ? $request->input('sort_by') 
                    : 'created_at'; // Default sort column
        $sortOrder = strtolower($request->input('sort_order', 'desc')) === 'asc' 
                    ? 'asc' 
                    : 'desc';      // Default sort direction
        $query->orderBy($sortBy, $sortOrder);
        // 4. DYNAMIC PAGINATION (Safety limit between 1 and 100)
        $perPage = min(max((int) $request->input('per_page', 10), 1), 100);
        $customers = $query->paginate($perPage);
        return response()->json([
            'success' => true,
            'data'    => $customers,
        ], Response::HTTP_OK);
    }

    /**
     * CREATE: Store a newly created customer
     * POST /api/customers
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name'    => 'required|string|max:255',
            'email'   => 'required|email|unique:customers,email|max:255',
            'phone'   => 'nullable|string|max:20',
            'address' => 'nullable|string|max:500',
        ]);

        $customer = Customer::create($validated);

        return response()->json([
            'success' => true,
            'message' => 'Customer created successfully',
            'data'    => $customer,
        ], Response::HTTP_CREATED);
    }

    /**
     * READ: Get a single customer by ID
     * GET /api/customers/{id}
     */
    public function show(Customer $customer): JsonResponse
    {
        return response()->json([
            'success' => true,
            'data'    => $customer,
        ], Response::HTTP_OK);
    }

    /**
     * UPDATE: Update customer details
     * PUT/PATCH /api/customers/{id}
     */
    public function update(Request $request, Customer $customer): JsonResponse
    {
        $validated = $request->validate([
            'name'    => 'sometimes|required|string|max:255',
            'email'   => 'sometimes|required|email|unique:customers,email,' . $customer->id . '|max:255',
            'phone'   => 'nullable|string|max:20',
            'address' => 'nullable|string|max:500',
        ]);

        $customer->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Customer updated successfully',
            'data'    => $customer,
        ], Response::HTTP_OK);
    }

    /**
     * DELETE: Delete a customer
     * DELETE /api/customers/{id}
     */
    public function destroy(Customer $customer): JsonResponse
    {
        $customer->delete();

        return response()->json([
            'success' => true,
            'message' => 'Customer deleted successfully',
        ], Response::HTTP_OK);
    }
}