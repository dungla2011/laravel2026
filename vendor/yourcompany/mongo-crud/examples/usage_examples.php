<?php

/**
 * MongoDB CRUD Package - Usage Examples
 * 
 * Các ví dụ sử dụng package MongoDB CRUD
 */

use YourCompany\MongoCrud\Models\Demo01;

// ============================================================================
// 1. BASIC CRUD OPERATIONS
// ============================================================================

// Tạo mới record
$newRecord = Demo01::create([
    'name' => 'Nguyễn Văn A',
    'email' => 'nguyenvana@example.com',
    'phone' => '0123456789',
    'address' => '123 Đường ABC, Quận 1, TP.HCM',
    'age' => 30,
    'status' => true,
    'description' => 'Nhân viên phát triển phần mềm',
    'metadata' => [
        'department' => 'IT',
        'position' => 'Senior Developer',
        'salary' => 25000000,
        'start_date' => '2024-01-15'
    ],
    'tags' => ['developer', 'php', 'laravel', 'mongodb']
]);

echo "Created record with ID: " . $newRecord->_id . "\n";

// Lấy record theo ID
$record = Demo01::find($newRecord->_id);
echo "Found record: " . $record->name . "\n";

// Cập nhật record
$record->update([
    'age' => 31,
    'metadata' => array_merge($record->metadata ?? [], [
        'last_promotion' => '2024-06-01'
    ])
]);

echo "Updated record age to: " . $record->age . "\n";

// Xóa record
// $record->delete();
// echo "Record deleted\n";

// ============================================================================
// 2. QUERY OPERATIONS
// ============================================================================

// Lấy tất cả records
$allRecords = Demo01::all();
echo "Total records: " . $allRecords->count() . "\n";

// Lấy records với điều kiện
$activeRecords = Demo01::where('status', true)->get();
echo "Active records: " . $activeRecords->count() . "\n";

// Sử dụng scopes
$activeAdults = Demo01::active()->ageRange(18, 65)->get();
echo "Active adults: " . $activeAdults->count() . "\n";

// Tìm kiếm theo tên
$searchResults = Demo01::search('name', 'Nguyễn')->get();
echo "Search results: " . $searchResults->count() . "\n";

// Lọc theo tag
$developers = Demo01::withTag('developer')->get();
echo "Developers: " . $developers->count() . "\n";

// Phân trang
$paginatedRecords = Demo01::paginate(10);
echo "Page 1 of " . $paginatedRecords->lastPage() . " pages\n";

// ============================================================================
// 3. ADVANCED QUERIES
// ============================================================================

// Query phức tạp với nhiều điều kiện
$complexQuery = Demo01::where('status', true)
    ->where('age', '>=', 25)
    ->where('age', '<=', 40)
    ->whereIn('tags', ['developer', 'manager'])
    ->orderBy('created_at', 'desc')
    ->limit(5)
    ->get();

echo "Complex query results: " . $complexQuery->count() . "\n";

// Aggregation
$stats = [
    'total' => Demo01::count(),
    'active' => Demo01::active()->count(),
    'inactive' => Demo01::inactive()->count(),
    'avg_age' => Demo01::whereNotNull('age')->avg('age'),
    'min_age' => Demo01::whereNotNull('age')->min('age'),
    'max_age' => Demo01::whereNotNull('age')->max('age'),
];

echo "Statistics:\n";
foreach ($stats as $key => $value) {
    echo "  {$key}: {$value}\n";
}

// Group by status
$groupedByStatus = Demo01::raw(function($collection) {
    return $collection->aggregate([
        [
            '$group' => [
                '_id' => '$status',
                'count' => ['$sum' => 1],
                'avg_age' => ['$avg' => '$age']
            ]
        ]
    ]);
});

echo "Grouped by status:\n";
foreach ($groupedByStatus as $group) {
    $status = $group['_id'] ? 'Active' : 'Inactive';
    echo "  {$status}: {$group['count']} records, avg age: " . round($group['avg_age'], 1) . "\n";
}

// ============================================================================
// 4. BULK OPERATIONS
// ============================================================================

// Tạo nhiều records cùng lúc
$bulkData = [
    [
        'name' => 'Trần Thị B',
        'email' => 'tranthib@example.com',
        'age' => 28,
        'status' => true,
        'tags' => ['designer', 'ui/ux']
    ],
    [
        'name' => 'Lê Văn C',
        'email' => 'levanc@example.com',
        'age' => 35,
        'status' => true,
        'tags' => ['manager', 'project-manager']
    ],
    [
        'name' => 'Phạm Thị D',
        'email' => 'phamthid@example.com',
        'age' => 26,
        'status' => false,
        'tags' => ['tester', 'qa']
    ]
];

foreach ($bulkData as $data) {
    Demo01::create($data);
}

echo "Created " . count($bulkData) . " bulk records\n";

// Cập nhật nhiều records
$updateCount = Demo01::where('age', '<', 30)->update(['status' => true]);
echo "Updated {$updateCount} records\n";

// Xóa nhiều records (cẩn thận!)
// $deleteCount = Demo01::where('status', false)->delete();
// echo "Deleted {$deleteCount} records\n";

// ============================================================================
// 5. WORKING WITH METADATA AND ARRAYS
// ============================================================================

// Tìm kiếm trong metadata
$itEmployees = Demo01::where('metadata.department', 'IT')->get();
echo "IT employees: " . $itEmployees->count() . "\n";

// Tìm kiếm trong array tags
$phpDevelopers = Demo01::where('tags', 'php')->get();
echo "PHP developers: " . $phpDevelopers->count() . "\n";

// Cập nhật metadata
Demo01::where('metadata.department', 'IT')
    ->update([
        'metadata.bonus' => 2000000,
        'metadata.updated_at' => now()->toISOString()
    ]);

echo "Updated IT employees metadata\n";

// Thêm tag mới
$record = Demo01::first();
if ($record) {
    $currentTags = $record->tags ?? [];
    $newTags = array_unique(array_merge($currentTags, ['senior', 'team-lead']));
    $record->update(['tags' => $newTags]);
    echo "Added new tags to record\n";
}

// ============================================================================
// 6. DATE OPERATIONS
// ============================================================================

// Lọc theo ngày tạo
$recentRecords = Demo01::where('created_at', '>=', now()->subDays(7))->get();
echo "Records created in last 7 days: " . $recentRecords->count() . "\n";

// Lọc theo khoảng thời gian
$thisMonth = Demo01::dateRange('created_at', 
    now()->startOfMonth(), 
    now()->endOfMonth()
)->get();
echo "Records created this month: " . $thisMonth->count() . "\n";

// ============================================================================
// 7. CUSTOM ATTRIBUTES AND METHODS
// ============================================================================

$record = Demo01::first();
if ($record) {
    // Sử dụng custom attribute
    $fullInfo = $record->full_info;
    echo "Full info:\n";
    foreach ($fullInfo as $key => $value) {
        echo "  {$key}: {$value}\n";
    }
}

// ============================================================================
// 8. ERROR HANDLING
// ============================================================================

try {
    // Thử tạo record với dữ liệu không hợp lệ
    $invalidRecord = Demo01::create([
        'name' => '', // Tên trống
        'email' => 'invalid-email', // Email không hợp lệ
        'age' => -5 // Tuổi âm
    ]);
} catch (\Exception $e) {
    echo "Error creating invalid record: " . $e->getMessage() . "\n";
}

try {
    // Thử tìm record không tồn tại
    $nonExistentRecord = Demo01::findOrFail('invalid-id');
} catch (\Exception $e) {
    echo "Record not found: " . $e->getMessage() . "\n";
}

// ============================================================================
// 9. PERFORMANCE TIPS
// ============================================================================

// Sử dụng select để chỉ lấy các field cần thiết
$lightweightRecords = Demo01::select(['name', 'email', 'status'])->get();
echo "Lightweight records: " . $lightweightRecords->count() . "\n";

// Sử dụng chunk để xử lý dữ liệu lớn
Demo01::chunk(100, function ($records) {
    foreach ($records as $record) {
        // Xử lý từng record
        // echo "Processing: " . $record->name . "\n";
    }
});

// Sử dụng cursor cho memory efficiency
foreach (Demo01::cursor() as $record) {
    // Xử lý từng record mà không load hết vào memory
    // echo "Cursor processing: " . $record->name . "\n";
    break; // Chỉ demo 1 record
}

echo "\n=== MongoDB CRUD Package Examples Completed ===\n"; 