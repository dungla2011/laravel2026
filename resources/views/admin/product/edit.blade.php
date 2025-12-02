@extends("layouts.adm")

@section("title")
 Edit product @endsection

@section("css")
    <link href="{{asset("vendor/select2/select2.min.css")}}" rel="stylesheet" />
    <link href="{{asset("admins/product/add.css")}}" rel="stylesheet" />
@endsection
@section('header')
    @include('parts.header-all')
@endsection

@section("content")
    <div class="content-wrapper">
        <!-- Content Header (Page header) -->
        <div class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1 class="m-0">Edit</h1>
                    </div><!-- /.col -->
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item" ppp085790374958><a href="#">Edit</a></li>
                            <li class="breadcrumb-item active"></li>
                        </ol>
                    </div><!-- /.col -->
                </div><!-- /.row -->
            </div><!-- /.container-fluid -->
        </div>
        <!-- /.content-header -->

        <div class="col-md-12">
            @if ($errors->any())
                <div class="alert alert-danger">
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif
        </div>

        <!-- Main content -->
        <form action="{{ route("admin.product.update" , ['id'=>$data->id]) }}"
              method="post" enctype="multipart/form-data">

            <section class="content">
                <div class="container-fluid">
                    <div class="row">
                        <div class="col-md-6">
                            @csrf
                            <div class="form-group">
                                <label for="">Tên san pham</label>
                                <input type="text"
                                       class="form-control @error('name') is-invalid @enderror"
                                       name="name"
                                       placeholder="Nhập tên san pham"
                                       value="{{old('name', $data->name)}}"
                                >
                                @error('name')
                                <div class="alert alert-danger">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="form-group">
                                <label for="">Giá</label>
                                <input type="text"
                                       class="form-control @error('price') is-invalid @enderror"
                                       name="price"
                                       value="{{old('price', $data->price)}}"
                                       placeholder="Nhập giá">
                                @error('price')
                                <div class="alert alert-danger">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="form-group">
                                <label for="">Ảnh đại diện</label>
                                <input type="file" class="form-control-file @error('feature_image_path') is-invalid @enderror"
                                       name="feature_image_path"
                                       placeholder="Nhập file">
                                <div class="col-md-4 feature_image_container">
                                    <div class="row">
                                        <img class="feature_image" src="{{$data->feature_image_path}}" alt="">
                                    </div>

                                </div>
                                @error('feature_image_path')
                                <div class="alert alert-danger">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="form-group">
                                <label for="">Ảnh chi tiet</label>
                                <input type="file"
                                       multiple
                                       class="form-control-file @error('image_path') is-invalid @enderror"
                                       name="image_path[]"
                                       placeholder="Nhập file">

                                <div class="col-md-12 container_image_detail">
                                    <div class="row">
                                        @foreach($data->productImages AS $productImg)
                                        <div class="col-md-3">
                                        <img class="image_detail_product" src="{{$productImg->image_path}}" alt="">
                                        </div>
                                        @endforeach
                                    </div>

                                </div>
                                @error('image_path')
                                <div class="alert alert-danger">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="form-group">
                                <label for="">Danh mục</label>
                                <select class="form-control @error('category_id') is-invalid @enderror" name="category_id" id="">
                                    <option value=""> - Chọn danh muc -</option>
                                    {!!  $htmlOption !!}
                                </select>
                                @error('category_id')
                                <div class="alert alert-danger">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="form-group">
                                <label for="">Tags</label>
                                <br>
                                <select name="tags[]" class="form-control tags_select_choose @error('tags') is-invalid @enderror" multiple="multiple">
                                    @foreach($data->tags AS $tagItem)
                                    <option value="{{$tagItem->name}}" selected>{{$tagItem->name}}</option>
                                    @endforeach
                                </select>
                                @error('tags')
                                <div class="alert alert-danger">{{ $message }}</div>
                                @enderror
                            </div>

                        </div>
                        <div class="col-md-12">
                            <div class="form-group">
                                <label for="">Nhap noi dung</label>
                                <textarea
                                    class="form-control tinymce_editor_init @error('contents') is-invalid @enderror" rows="5"
                                          name="contents">{{$data->content}}</textarea>
                            </div>

                            @error('contents')
                            <div class="alert alert-danger">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-12">
                            <button type="submit" class="btn btn-primary">Submit</button>
                        </div>

                    </div>
                </div>
            </section>
        </form>
        <!-- /.content -->
    </div>
@endsection


@section("js")
    <script src="{{asset("vendor/select2/select2.min.js")}}"></script>
    <script src="{{asset("vendor/tinymce/tinymce48.min.js")}}"></script>

    <script src="{{asset("admins/product/add.js")}}"></script>

@endsection
