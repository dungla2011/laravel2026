<?php

namespace YourCompany\MongoCrud\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use YourCompany\MongoCrud\Models\Demo01;
use Illuminate\Validation\ValidationException;

class Demo01Controller extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        try {
            $query = Demo01::query();

            // Search by name
            if ($request->has('search')) {
                $query->search('name', $request->search);
            }

            // Filter by status
            if ($request->has('status')) {
                if ($request->status === 'active') {
                    $query->active();
                } elseif ($request->status === 'inactive') {
                    $query->inactive();
                }
            }

            // Filter by age range
            if ($request->has('min_age') || $request->has('max_age')) {
                $query->ageRange($request->min_age, $request->max_age);
            }

            // Filter by tag
            if ($request->has('tag')) {
                $query->withTag($request->tag);
            }

            // Date range filter
            if ($request->has('start_date') || $request->has('end_date')) {
                $query->dateRange('created_at', $request->start_date, $request->end_date);
            }

            // Sort
            $sortBy = $request->get('sort_by', 'created_at');
            $sortOrder = $request->get('sort_order', 'desc');
            $query->orderBy($sortBy, $sortOrder);

            // Pagination
            $perPage = $request->get('per_page', config('mongocrud.pagination.per_page', 20));
            $data = $query->paginate($perPage);

            return response()->json([
                'success' => true,
                'data' => $data,
                'message' => 'Records retrieved successfully'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error retrieving records: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {
            $validatedData = $request->validate([
                'name' => 'required|string|max:255',
                'email' => 'required|email|max:255',
                'phone' => 'nullable|string|max:20',
                'address' => 'nullable|string|max:500',
                'age' => 'nullable|integer|min:0|max:150',
                'status' => 'nullable|boolean',
                'description' => 'nullable|string|max:1000',
                'metadata' => 'nullable|array',
                'tags' => 'nullable|array'
            ]);

            // Set default values
            $validatedData['status'] = $validatedData['status'] ?? true;

            $record = Demo01::create($validatedData);

            return response()->json([
                'success' => true,
                'data' => $record,
                'message' => 'Record created successfully'
            ], 201);

        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error creating record: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        try {
            $record = Demo01::find($id);

            if (!$record) {
                return response()->json([
                    'success' => false,
                    'message' => 'Record not found'
                ], 404);
            }

            return response()->json([
                'success' => true,
                'data' => $record,
                'message' => 'Record retrieved successfully'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error retrieving record: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        try {
            $record = Demo01::find($id);

            if (!$record) {
                return response()->json([
                    'success' => false,
                    'message' => 'Record not found'
                ], 404);
            }

            $validatedData = $request->validate([
                'name' => 'sometimes|required|string|max:255',
                'email' => 'sometimes|required|email|max:255',
                'phone' => 'nullable|string|max:20',
                'address' => 'nullable|string|max:500',
                'age' => 'nullable|integer|min:0|max:150',
                'status' => 'nullable|boolean',
                'description' => 'nullable|string|max:1000',
                'metadata' => 'nullable|array',
                'tags' => 'nullable|array'
            ]);

            $record->update($validatedData);

            return response()->json([
                'success' => true,
                'data' => $record->fresh(),
                'message' => 'Record updated successfully'
            ]);

        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error updating record: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        try {
            $record = Demo01::find($id);

            if (!$record) {
                return response()->json([
                    'success' => false,
                    'message' => 'Record not found'
                ], 404);
            }

            $record->delete();

            return response()->json([
                'success' => true,
                'message' => 'Record deleted successfully'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error deleting record: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get statistics
     */
    public function stats()
    {
        try {
            $stats = [
                'total' => Demo01::count(),
                'active' => Demo01::active()->count(),
                'inactive' => Demo01::inactive()->count(),
                'recent' => Demo01::where('created_at', '>=', now()->subDays(7))->count(),
                'avg_age' => Demo01::whereNotNull('age')->avg('age'),
            ];

            return response()->json([
                'success' => true,
                'data' => $stats,
                'message' => 'Statistics retrieved successfully'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error retrieving statistics: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Bulk operations
     */
    public function bulk(Request $request)
    {
        try {
            $action = $request->get('action');
            $ids = $request->get('ids', []);

            if (empty($ids)) {
                return response()->json([
                    'success' => false,
                    'message' => 'No IDs provided'
                ], 400);
            }

            switch ($action) {
                case 'delete':
                    $count = Demo01::whereIn('_id', $ids)->delete();
                    $message = "{$count} records deleted successfully";
                    break;

                case 'activate':
                    $count = Demo01::whereIn('_id', $ids)->update(['status' => true]);
                    $message = "{$count} records activated successfully";
                    break;

                case 'deactivate':
                    $count = Demo01::whereIn('_id', $ids)->update(['status' => false]);
                    $message = "{$count} records deactivated successfully";
                    break;

                default:
                    return response()->json([
                        'success' => false,
                        'message' => 'Invalid action'
                    ], 400);
            }

            return response()->json([
                'success' => true,
                'message' => $message,
                'affected_count' => $count
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error performing bulk operation: ' . $e->getMessage()
            ], 500);
        }
    }
} 