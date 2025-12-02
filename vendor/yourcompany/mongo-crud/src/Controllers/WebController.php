<?php

namespace YourCompany\MongoCrud\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use YourCompany\MongoCrud\Models\Demo01;

class WebController extends Controller
{
    /**
     * Dashboard page
     */
    public function dashboard()
    {
        try {
            $stats = [
                'total' => Demo01::count(),
                'active' => Demo01::active()->count(),
                'inactive' => Demo01::inactive()->count(),
                'recent' => Demo01::where('created_at', '>=', now()->subDays(7))->count(),
                'avg_age' => Demo01::whereNotNull('age')->avg('age'),
            ];

            $recentRecords = Demo01::orderBy('created_at', 'desc')->limit(10)->get();

            return view('mongocrud::dashboard', compact('stats', 'recentRecords'));
        } catch (\Exception $e) {
            return view('mongocrud::dashboard', [
                'stats' => [
                    'total' => 0,
                    'active' => 0,
                    'inactive' => 0,
                    'recent' => 0,
                    'avg_age' => 0,
                ],
                'recentRecords' => collect(),
                'error' => $e->getMessage()
            ]);
        }
    }

    /**
     * Demo01 index page
     */
    public function demo01Index(Request $request)
    {
        try {
            $query = Demo01::query();

            // Search
            if ($request->has('search') && !empty($request->search)) {
                $query->search('name', $request->search);
            }

            // Filter by status
            if ($request->has('status') && $request->status !== '') {
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

            // Sort
            $sortBy = $request->get('sort_by', 'created_at');
            $sortOrder = $request->get('sort_order', 'desc');
            $query->orderBy($sortBy, $sortOrder);

            // Pagination
            $perPage = $request->get('per_page', 20);
            $records = $query->paginate($perPage);

            // Get unique tags for filter
            $allTags = Demo01::whereNotNull('tags')->pluck('tags')->flatten()->unique()->sort()->values();

            return view('mongocrud::demo01.index', compact('records', 'allTags'));
        } catch (\Exception $e) {
            return view('mongocrud::demo01.index', [
                'records' => collect(),
                'allTags' => collect(),
                'error' => $e->getMessage()
            ]);
        }
    }

    /**
     * Demo01 create page
     */
    public function demo01Create()
    {
        return view('mongocrud::demo01.create');
    }

    /**
     * Demo01 show page
     */
    public function demo01Show($id)
    {
        try {
            $record = Demo01::find($id);

            if (!$record) {
                abort(404, 'Record not found');
            }

            return view('mongocrud::demo01.show', compact('record'));
        } catch (\Exception $e) {
            abort(500, 'Error loading record: ' . $e->getMessage());
        }
    }

    /**
     * Demo01 edit page
     */
    public function demo01Edit($id)
    {
        try {
            $record = Demo01::find($id);

            if (!$record) {
                abort(404, 'Record not found');
            }

            return view('mongocrud::demo01.edit', compact('record'));
        } catch (\Exception $e) {
            abort(500, 'Error loading record: ' . $e->getMessage());
        }
    }
} 