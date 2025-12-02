{{-- resources/views/media/items/index.blade.php --}}
@extends("layouts.adm")
@section("title")
    Media Item - Danh sách
@endsection
@section('header')
    <link href="{{asset("vendor/select2/select2.min.css")}}" rel="stylesheet" />
    <link href="{{asset("vendor/toastr/toastr.min.css")}}" rel="stylesheet" />

    @include('parts.header-all')
@endsection

@section('content')
    <div class="content-wrapper p-2 pt-3">
    <div class="container-fluid" style="">
        <div class="row justify-content-center" >
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <span>Danh sách Media Items</span>
                        <a href="{{ route('media.items.create') }}" class="btn btn-primary btn-sm">Thêm mới</a>
                    </div>

                    <div class="card-body">
                        @if (session('success'))
                            <div class="alert alert-success">{{ session('success') }}</div>
                        @endif

                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Tên</th>
                                    <th>Folder chính</th>
                                    <th>Các folder khác</th>
                                    <th>Ngày tạo</th>
                                    <th>Thao tác</th>
                                </tr>
                                </thead>
                                <tbody>
                                @foreach ($items as $item)
                                    <tr>
                                        <td>{{ $item->id }}</td>
                                        <td>{{ $item->name }}</td>
                                        <td>
                                            @foreach ($item->folders as $folder)
                                                @if ($folder->pivot->is_primary)
                                                    <span class="badge bg-primary">{{ $folder->name }}</span>
                                                @endif
                                            @endforeach
                                        </td>
                                        <td>
                                            @foreach ($item->folders as $folder)
                                                @unless ($folder->pivot->is_primary)
                                                    <span class="badge bg-secondary">{{ $folder->name }}</span>
                                                @endunless
                                            @endforeach
                                        </td>
                                        <td>
                                            @if ($item->created_at)
                                                {{ $item->created_at->format('d/m/Y H:i') }}
                                            @else
                                                --
                                            @endif
                                        </td>
                                        <td>
                                            <div class="btn-group">
                                                <a href="{{ route('media.items.show', $item) }}" class="btn btn-info btn-sm">Xem</a>
                                                <a href="{{ route('media.items.edit', $item) }}" class="btn btn-warning btn-sm">Sửa</a>
                                                <form action="{{ route('media.items.destroy', $item) }}" method="POST" onsubmit="return confirm('Bạn có chắc chắn muốn xóa?');">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-danger btn-sm">Xóa</button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                        </div>

                        <div class="d-flex justify-content-center">
                            {{ $items->links() }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    </div>
@endsection
