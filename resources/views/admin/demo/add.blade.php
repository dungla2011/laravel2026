@extends("layouts.adm")

@section("title")
     ADD
@endsection
@section('header')
    <link href="{{asset("vendor/select2/select2.min.css")}}" rel="stylesheet" />
    <link href="{{asset("vendor/toastr/toastr.min.css")}}" rel="stylesheet" />

    @include('parts.header-all')
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
                        <h1 class="m-0">Add</h1>
                    </div><!-- /.col -->
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item" data-code-pos='ppp1725635151'><a href="#">ADD</a></li>
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
                        <form  id="post_data_form" data-url="{{ route("api.demo.add") }}">
                            @csrf
                            <div class="form-group">
                                <label for="">Nhap number1</label>
                                <input type="text" class="form-control"
                                       name="number1"
                                       placeholder="Nhập  number1"
                                >
                            </div>
                            <div class="form-group">
                                <label for="">Nhap number2</label>
                                <input type="text" class="form-control"
                                       name="number2"
                                       placeholder="Nhập  number2"
                                >
                            </div>

                            <div class="form-group">
                                <label for="">Nhap string1</label>
                                <input type="text" class="form-control"
                                       name="string1"
                                       placeholder="Nhập  string1"
                                >
                            </div>

                            <div class="form-group">
                                <label for="">Nhap string2</label>
                                <input type="text" class="form-control"
                                       name="string2"
                                       placeholder="Nhập  string2"
                                >
                            </div>


                            <div class="form-group">
                                <label>Nhập tags demo </label>
                                <select name="tags[]" class="form-control tags_select_choose" multiple="multiple">
                                </select>
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
