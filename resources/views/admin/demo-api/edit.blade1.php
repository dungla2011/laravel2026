@extends("layouts.adm")

@section("title")
    Edit
@endsection
@section('header')
    @include('parts.header-all')
    <link href="{{asset("vendor/select2/select2.min.css")}}" rel="stylesheet" />
    <link href="{{asset("vendor/toastr/toastr.min.css")}}" rel="stylesheet" />
@endsection
<style>
    .select2-selection__choice{
        background-color: #343a40!important;
    }
    .select2-container--default .select2-selection--multiple .select2-selection__choice__display {
        padding-left: 7px;
    }

</style>
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
                            <li class="breadcrumb-item" data-code-pos='ppp172351148551'><a href="#">Edit</a></li>
                            <li class="breadcrumb-item active"></li>
                        </ol>
                    </div><!-- /.col -->
                </div><!-- /.row -->
            </div><!-- /.container-fluid -->
        </div>
        <!-- /.content-header -->

        <!-- Main content -->
        <section class="content">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-md-6">
                    <form id="post_data_form" data-url="{{ route("api.demo.update", ['id'=>$data->id]) }}">
                        @csrf
                            <div class="form-group">
                                <label for="">Nhap number1</label>
                                <input type="text" class="form-control"
                                       name="number1"
                                       placeholder="Nhập  number1"
                                       value="{{old('number1', $data->number1)}}"
                                >
                            </div>
                            <div class="form-group">
                                <label for="">Nhap number2</label>
                                <input type="text" class="form-control"
                                       name="number2"
                                       placeholder="Nhập  number2"
                                       value="{{old('number2', $data->number2)}}"
                                >
                            </div>

                            <div class="form-group">
                                <label for="">Nhap string1</label>
                                <input type="text" class="form-control"
                                       name="string1"
                                       placeholder="Nhập  string1"
                                       value="{{old('string1', $data->string1)}}"
                                >
                            </div>

                            <div class="form-group">
                                <label for="">Nhap string2</label>
                                <input type="text" class="form-control"
                                       name="string2"
                                       placeholder="Nhập  string2"
                                       value="{{old('string2', $data->string2)}}"
                                >
                            </div>

                            <div class="form-group">
                                <label for="">SubVal</label>

                                @foreach($data->sub1 AS $sub)
                                    <label for="">
                                    <input checked type="checkbox" name="sub_value[]" value="{{$sub->sub_val}} ">
                                        {{$sub->sub_val}}
                                    </label>
                                @endforeach

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
                        <button type="submit" class="btn btn-primary">Submit</button>
                    </form>
                    </div>
                </div>
            </div>
        </section>
        <!-- /.content -->
    </div>
@endsection

@section("js")
    <script src="{{asset("vendor/select2/select2.min.js")}}"></script>
    <script src="{{asset("vendor/toastr/toastr.min.js")}}"></script>
    <script src="{{asset("admins/demo/add.js")}}"></script>
@endsection
