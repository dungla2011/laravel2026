<?php

namespace App\Http\Controllers;

use App\Models\MediaItem2;
use App\Models\MediaFolder2;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class MediaItemController2 extends Controller
{
    /**
     * Hiển thị danh sách các item
     */
    public function index()
    {
        $items = MediaItem2::with('folders')->paginate(15);
        return view('media.items.index', compact('items'));
    }

    /**
     * Hiển thị form tạo item mới
     */
    public function create()
    {
        $folders = MediaFolder2::all();
        return view('media.items.create', compact('folders'));
    }

    /**
     * Lưu item mới vào database
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            // Thêm validation cho các trường khác
            'folders' => 'required|array',
            'folders.*' => 'exists:media_folders,id',
            'primary_folder_id' => 'required|exists:media_folders,id',
        ]);

        DB::beginTransaction();
        try {
            // Tạo mới item
            $item = MediaItem2::create([
                'name' => $validated['name'],
                'description' => $validated['description'],
                // Thêm các trường khác
            ]);

            // Gán folders cho item
            foreach ($validated['folders'] as $folderId) {
                $item->folders()->attach($folderId, [
                    'is_primary' => $folderId == $validated['primary_folder_id']
                ]);
            }

            DB::commit();
            return redirect()->route('media.items.index')
                ->with('success', 'Item được tạo thành công!');
        } catch (\Exception $e) {
            DB::rollback();
            return back()->with('error', 'Có lỗi xảy ra: ' . $e->getMessage())->withInput();
        }
    }

    /**
     * Hiển thị chi tiết item
     */
    public function show(MediaItem2 $item)
    {
        $item->load('folders');
        $primaryFolder = $item->primaryFolder();

        return view('media.items.show', compact('item', 'primaryFolder'));
    }

    /**
     * Hiển thị form chỉnh sửa item
     */
    public function edit(MediaItem2 $item)
    {
        $folders = MediaFolder2::all();
        $item->load('folders');
        $primaryFolder = $item->primaryFolder();
        $selectedFolders = $item->folders->pluck('id')->toArray();

        return view('media.items.edit', compact('item', 'folders', 'primaryFolder', 'selectedFolders'));
    }

    /**
     * Cập nhật item trong database
     */
    public function update(Request $request, MediaItem2 $item)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            // Validation các trường khác
            'folders' => 'required|array',
            'folders.*' => 'exists:media_folders,id',
            'primary_folder_id' => 'required|exists:media_folders,id',
        ]);

        DB::beginTransaction();
        try {
            // Cập nhật thông tin item
            $item->update([
                'name' => $validated['name'],
                'description' => $validated['description'],
                // Cập nhật các trường khác
            ]);

            // Xóa tất cả các liên kết folders hiện tại và tạo lại
            $item->folders()->detach();

            // Gán lại folders cho item
            foreach ($validated['folders'] as $folderId) {
                $item->folders()->attach($folderId, [
                    'is_primary' => $folderId == $validated['primary_folder_id']
                ]);
            }

            DB::commit();
            return redirect()->route('media.items.index')
                ->with('success', 'Item được cập nhật thành công!');
        } catch (\Exception $e) {
            DB::rollback();
            return back()->with('error', 'Có lỗi xảy ra: ' . $e->getMessage())->withInput();
        }
    }

    /**
     * Xóa item khỏi database
     */
    public function destroy(MediaItem2 $item)
    {
        try {
            // Item và các liên kết folders sẽ bị xóa (nhờ onDelete cascade)
            $item->delete();
            return redirect()->route('media.items.index')
                ->with('success', 'Item đã được xóa thành công!');
        } catch (\Exception $e) {
            return back()->with('error', 'Có lỗi xảy ra: ' . $e->getMessage());
        }
    }

    /**
     * Lấy items theo folder id
     */
    public function getItemsByFolder($folderId)
    {
        $folder = MediaFolder2::findOrFail($folderId);
        $items = $folder->items()->paginate(15);

        return view('media.folders.items', compact('folder', 'items'));
    }
}
