<?php

namespace App\Http\Controllers;

use App\Models\TestMongo1;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class TestMongo1Controller extends Controller
{
    /**
     * Display a listing of the resource with pagination.
     */
    public function index(Request $request)
    {
        $query = TestMongo1::query();
        
        // Search functionality
        if ($request->has('search') && !empty($request->search)) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%");
            });
        }
        
        // Sorting
        $sortBy = $request->get('sort_by', 'created_at');
        $sortOrder = $request->get('sort_order', 'desc');
        $query->orderBy($sortBy, $sortOrder);
        
        // Pagination
        $perPage = $request->get('per_page', 10);
        $testMongo1s = $query->paginate($perPage);
        
        return view('testmongo1.index', compact('testMongo1s'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('testmongo1.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|email',
            'phone' => 'nullable|string|max:20',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048'
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        // Check email uniqueness manually for MongoDB
        $existingEmail = TestMongo1::where('email', $request->email)->first();
        if ($existingEmail) {
            return redirect()->back()
                ->withErrors(['email' => 'The email has already been taken.'])
                ->withInput();
        }

        $data = $request->only(['name', 'email', 'phone']);
        
        // Handle image upload
        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $imageName = time() . '_' . $image->getClientOriginalName();
            $imagePath = $image->storeAs('testmongo1', $imageName, 'public');
            $data['image'] = $imagePath;
        }

        TestMongo1::create($data);

        return redirect()->route('testmongo1.index')
            ->with('success', 'Record created successfully!');
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $testMongo1 = TestMongo1::findOrFail($id);
        return view('testmongo1.show', compact('testMongo1'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        $testMongo1 = TestMongo1::findOrFail($id);
        return view('testmongo1.edit', compact('testMongo1'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $testMongo1 = TestMongo1::findOrFail($id);
        
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|email',
            'phone' => 'nullable|string|max:20',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048'
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        // Check email uniqueness manually for MongoDB (exclude current record)
        $existingEmail = TestMongo1::where('email', $request->email)
                                  ->where('_id', '!=', $id)
                                  ->first();
        if ($existingEmail) {
            return redirect()->back()
                ->withErrors(['email' => 'The email has already been taken.'])
                ->withInput();
        }

        $data = $request->only(['name', 'email', 'phone']);
        
        // Handle image upload
        if ($request->hasFile('image')) {
            // Delete old image if exists
            if ($testMongo1->image && Storage::disk('public')->exists($testMongo1->image)) {
                Storage::disk('public')->delete($testMongo1->image);
            }
            
            $image = $request->file('image');
            $imageName = time() . '_' . $image->getClientOriginalName();
            $imagePath = $image->storeAs('testmongo1', $imageName, 'public');
            $data['image'] = $imagePath;
        }

        $testMongo1->update($data);

        return redirect()->route('testmongo1.index')
            ->with('success', 'Record updated successfully!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $testMongo1 = TestMongo1::findOrFail($id);
        
        // Delete image if exists
        if ($testMongo1->image && Storage::disk('public')->exists($testMongo1->image)) {
            Storage::disk('public')->delete($testMongo1->image);
        }
        
        $testMongo1->delete();

        return redirect()->route('testmongo1.index')
            ->with('success', 'Record deleted successfully!');
    }

    /**
     * API methods for AJAX requests
     */
    public function apiIndex(Request $request)
    {
        $query = TestMongo1::query();
        
        if ($request->has('search') && !empty($request->search)) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%");
            });
        }
        
        $sortBy = $request->get('sort_by', 'created_at');
        $sortOrder = $request->get('sort_order', 'desc');
        $query->orderBy($sortBy, $sortOrder);
        
        $perPage = $request->get('per_page', 10);
        $testMongo1s = $query->paginate($perPage);
        
        return response()->json($testMongo1s);
    }

    public function apiStore(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|email',
            'phone' => 'nullable|string|max:20',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        // Check email uniqueness manually for MongoDB
        $existingEmail = TestMongo1::where('email', $request->email)->first();
        if ($existingEmail) {
            return response()->json([
                'success' => false,
                'errors' => ['email' => ['The email has already been taken.']]
            ], 422);
        }

        $data = $request->only(['name', 'email', 'phone']);
        
        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $imageName = time() . '_' . $image->getClientOriginalName();
            $imagePath = $image->storeAs('testmongo1', $imageName, 'public');
            $data['image'] = $imagePath;
        }

        $testMongo1 = TestMongo1::create($data);

        return response()->json([
            'success' => true,
            'data' => $testMongo1,
            'message' => 'Record created successfully!'
        ], 201);
    }

    public function apiShow($id)
    {
        $testMongo1 = TestMongo1::findOrFail($id);
        return response()->json([
            'success' => true,
            'data' => $testMongo1
        ]);
    }

    public function apiUpdate(Request $request, $id)
    {
        $testMongo1 = TestMongo1::findOrFail($id);
        
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|email',
            'phone' => 'nullable|string|max:20',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        // Check email uniqueness manually for MongoDB (exclude current record)
        $existingEmail = TestMongo1::where('email', $request->email)
                                  ->where('_id', '!=', $id)
                                  ->first();
        if ($existingEmail) {
            return response()->json([
                'success' => false,
                'errors' => ['email' => ['The email has already been taken.']]
            ], 422);
        }

        $data = $request->only(['name', 'email', 'phone']);
        
        if ($request->hasFile('image')) {
            if ($testMongo1->image && Storage::disk('public')->exists($testMongo1->image)) {
                Storage::disk('public')->delete($testMongo1->image);
            }
            
            $image = $request->file('image');
            $imageName = time() . '_' . $image->getClientOriginalName();
            $imagePath = $image->storeAs('testmongo1', $imageName, 'public');
            $data['image'] = $imagePath;
        }

        $testMongo1->update($data);

        return response()->json([
            'success' => true,
            'data' => $testMongo1,
            'message' => 'Record updated successfully!'
        ]);
    }

    public function apiDestroy($id)
    {
        $testMongo1 = TestMongo1::findOrFail($id);
        
        if ($testMongo1->image && Storage::disk('public')->exists($testMongo1->image)) {
            Storage::disk('public')->delete($testMongo1->image);
        }
        
        $testMongo1->delete();

        return response()->json([
            'success' => true,
            'message' => 'Record deleted successfully!'
        ]);
    }

    /**
     * Additional utility methods
     */
    
    /**
     * Duplicate a record
     */
    public function duplicate($id)
    {
        $original = TestMongo1::findOrFail($id);
        
        $duplicate = $original->replicate();
        $duplicate->name = $original->name . ' (Copy)';
        $duplicate->email = 'copy_' . time() . '_' . $original->email;
        $duplicate->save();

        return redirect()->route('testmongo1.edit', $duplicate->_id)
            ->with('success', 'Record duplicated successfully! Please update the details.');
    }

    /**
     * Bulk delete records
     */
    public function bulkDelete(Request $request)
    {
        $ids = $request->input('ids', []);
        
        if (empty($ids)) {
            return response()->json([
                'success' => false,
                'message' => 'No records selected for deletion.'
            ], 400);
        }

        $deleted = 0;
        foreach ($ids as $id) {
            try {
                $record = TestMongo1::find($id);
                if ($record) {
                    // Delete image if exists
                    if ($record->image && Storage::disk('public')->exists($record->image)) {
                        Storage::disk('public')->delete($record->image);
                    }
                    $record->delete();
                    $deleted++;
                }
            } catch (\Exception $e) {
                // Log error but continue with other deletions
                \Log::error("Failed to delete record {$id}: " . $e->getMessage());
            }
        }

        return response()->json([
            'success' => true,
            'message' => "{$deleted} record(s) deleted successfully."
        ]);
    }

    /**
     * Export records as CSV
     */
    public function exportCsv(Request $request)
    {
        $query = TestMongo1::query();
        
        // Apply same filters as index
        if ($request->has('search') && !empty($request->search)) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%");
            });
        }
        
        $records = $query->get();
        
        $filename = 'testmongo1_export_' . date('Y-m-d_H-i-s') . '.csv';
        
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ];

        $callback = function() use ($records) {
            $file = fopen('php://output', 'w');
            
            // CSV Headers
            fputcsv($file, ['ID', 'Name', 'Email', 'Phone', 'Image', 'Created At', 'Updated At']);
            
            // CSV Data
            foreach ($records as $record) {
                fputcsv($file, [
                    $record->_id,
                    $record->name,
                    $record->email,
                    $record->phone ?? '',
                    $record->image ?? '',
                    $record->created_at ? $record->created_at->format('Y-m-d H:i:s') : '',
                    $record->updated_at ? $record->updated_at->format('Y-m-d H:i:s') : '',
                ]);
            }
            
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Export records as JSON
     */
    public function exportJson(Request $request)
    {
        $query = TestMongo1::query();
        
        // Apply same filters as index
        if ($request->has('search') && !empty($request->search)) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%");
            });
        }
        
        $records = $query->get();
        
        $filename = 'testmongo1_export_' . date('Y-m-d_H-i-s') . '.json';
        
        $data = [
            'export_info' => [
                'exported_at' => now()->format('Y-m-d H:i:s'),
                'total_records' => $records->count(),
                'filters_applied' => $request->only(['search', 'sort_by', 'sort_order'])
            ],
            'records' => $records->map(function($record) {
                return [
                    'id' => $record->_id,
                    'name' => $record->name,
                    'email' => $record->email,
                    'phone' => $record->phone,
                    'image' => $record->image,
                    'created_at' => $record->created_at ? $record->created_at->format('Y-m-d H:i:s') : null,
                    'updated_at' => $record->updated_at ? $record->updated_at->format('Y-m-d H:i:s') : null,
                ];
            })
        ];

        return response()->json($data)
            ->header('Content-Disposition', "attachment; filename=\"{$filename}\"");
    }
}
