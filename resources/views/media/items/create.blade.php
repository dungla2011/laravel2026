@extends("layouts.adm")
@section("title")
    Media Item - Thêm mới
@endsection
@section('header')
    <link href="{{asset("vendor/select2/select2.min.css")}}" rel="stylesheet" />
    <link href="{{asset("vendor/toastr/toastr.min.css")}}" rel="stylesheet" />

    @include('parts.header-all')
@endsection


@section('content')
    <div class="content-wrapper p-2 pt-3">

    <div class="container-fluid" style="">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">Thêm Media Item mới</div>

                    <div class="card-body">
                        @if (session('error'))
                            <div class="alert alert-danger">{{ session('error') }}</div>
                        @endif

                        <form action="{{ route('media.items.store') }}" method="POST">
                            @csrf

                            <div class="form-group mb-3">
                                <label for="name">Tên</label>
                                <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name') }}" required>
                                @error('name')
                                <span class="invalid-feedback">{{ $message }}</span>
                                @enderror
                            </div>

                            <div class="form-group mb-3">
                                <label for="description">Mô tả</label>
                                <textarea class="form-control @error('description') is-invalid @enderror" id="description" name="description">{{ old('description') }}</textarea>
                                @error('description')
                                <span class="invalid-feedback">{{ $message }}</span>
                                @enderror
                            </div>

                            <div class="form-group mb-3">
                                <label>Folders</label>
                                <div class="mt-2">
                                    @foreach ($folders as $folder)
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" name="folders[]" id="folder{{ $folder->id }}" value="{{ $folder->id }}" {{ in_array($folder->id, old('folders', [])) ? 'checked' : '' }}>
                                            <label class="form-check-label" for="folder{{ $folder->id }}">
                                                {{ $folder->name }}
                                            </label>
                                        </div>
                                    @endforeach
                                </div>
                                @error('folders')
                                <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>

                            <div class="form-group mb-3">
                                <label for="primary_folder_id">Folder chính</label>
                                <select class="form-control @error('primary_folder_id') is-invalid @enderror" id="primary_folder_id" name="primary_folder_id">
                                    <option value="">-- Chọn folder chính --</option>
                                    @foreach ($folders as $folder)
                                        <option value="{{ $folder->id }}" {{ old('primary_folder_id') == $folder->id ? 'selected' : '' }}>{{ $folder->name }}</option>
                                    @endforeach
                                </select>
                                @error('primary_folder_id')
                                <span class="invalid-feedback">{{ $message }}</span>
                                @enderror
                            </div>

                            <div class="form-group">
                                <button type="submit" class="btn btn-primary">Lưu</button>
                                <a href="{{ route('media.items.index') }}" class="btn btn-secondary">Quay lại</a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Khi chọn folder chính, tự động chọn folder trong danh sách nếu chưa chọn
            document.getElementById('primary_folder_id').addEventListener('change', function() {
                const folderId = this.value;
                if (folderId) {
                    const checkbox = document.getElementById('folder' + folderId);
                    if (checkbox && !checkbox.checked) {
                        checkbox.checked = true;
                    }
                }
            });
        });
    </script>
@endsection
