<style>
    .table td, .table th {
        padding: 5px 0px 0px 0px !important;
    }
</style>
@extends("layouts.adm")

@section("title")
 Edit Role @endsection

@section("css")
    <link href="{{asset("vendor/select2/select2.min.css")}}" rel="stylesheet"/>
    <link href="{{asset("admins/product/add.css")}}" rel="stylesheet"/>

    <style>
        .content-wrapper {
            height: auto !important;
        }
    </style>
@endsection

@section("js")
    <script src="{{asset("vendor/select2/select2.min.js")}}"></script>
    <script src="{{asset("vendor/tinymce/tinymce48.min.js")}}"></script>
    <script src="{{asset("admins/role/add.js")}}"></script>

    <script>
        $("#save_all_route").on('click', function () {

            let mEnable = []
            $(".checkbox_child_route").each(function () {
                let val = $(this).val();
                let roleId = $(this).attr('data-role-id');
                let check = $(this).is(":checked");
                // console.log(" --- roleId = " + roleId);
                // console.log(" VAL = " + val);
                // console.log(" check = " + check);

                if (check)
                    mEnable.push(val);
            })

            let name = $("input[name='name']").val();
            let display_name = $("input[name='display_name']").val();
            let role_id = '{{ request('id')  }} ';

            let dataPost = {
                'name': name,
                'display_name': display_name,
                'route_name_code': mEnable,
                'id': role_id,
            }

            console.log(" All Enable ", mEnable);

            console.log(" Name = " + name);
            console.log(" display_name = " + display_name);
            console.log("dataPost = ", dataPost);
            let user_token = jctool.getCookie('_tglx863516839');

            let urlPost = "/api/admin-role/save-role";
            $.ajax({
                url: urlPost,
                type: 'POST',
                data: dataPost,
                beforeSend: function (xhr) {
                    xhr.setRequestHeader('Authorization', 'Bearer ' + user_token);
                },
                success: function (result) {
                    showToastInfoTop("DONE!")
                },
                error: function (result) {

                    alert("Error!");
                },
            });

        });
    </script>


@endsection
@section('header')
    @include('parts.header-all')
@endsection

@section("content")
    <?php


    ?>
    <div class="content-wrapper p-3">
        <!-- Content Header (Page header) -->
        <div class="content-header">
            <a target="_blank" href="/tool/auto-insert-route-permission"> Check Insert New Route</a>
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
        <form action="{{ route("admin.role.update", ['id'=>$data->id]) }}"
              method="post" enctype="multipart/form-data">
            <section class="content">
                <div class="container-fluid">

                    @if (\Session::has('success'))
                        <div class="alert alert-success">
                            <ul>
                                <li>{!! \Session::get('success') !!}</li>
                            </ul>
                        </div>
                    @endif


                    <div class="row">
                        <div class="col-md-6">

                            @csrf
                            <div class="form-group">
                                <label>Tên </label>
                                <input data-code-pos="ppp1667922777673" type="text"
                                       class="form-control @error('name') is-invalid @enderror"
                                       name="name"
                                       placeholder="Nhập tên"
                                       value="{{ old('name', $data->name) }}"
                                >
                                @error('name')
                                <div class="alert alert-danger">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="form-group">
                                <label>Display name </label>
                                <input type="text"
                                       class="form-control @error('display_name') is-invalid @enderror"
                                       name="display_name"
                                       placeholder="Nhập display_name"
                                       value="{{ old('display_name', $data->display_name) }}"
                                >
                                @error('display_name')
                                <div class="alert alert-danger">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="col-md-12 parent_all_check_per">


                            <label>

                                <input type="checkbox" class="checkall">
                                <b>Check All Role</b>
                            </label>


                            @foreach($permissionsParent AS $permission)

                                <div data-code-pos="ppp1667922768975" class="inside_a_parent">
                                    <b>

                                        <label title="{{$permission->route_name_code}}">
                                            <input
                                                name="route_name_code[]"
                                                value="{{$permission->route_name_code}}"
                                                title="{{$permission->route_name_code}}"
                                                type="checkbox" class="checkbox_wrapper">
                                            <b>
                                                Module {{  $permission->display_name  }} {{ $permission->display_name == $permission->name ? '' : " /" . $permission->name  }}
                                            </b>
                                        </label>
                                    </b>

                                    <table class="table">
                                        @foreach($permission->permissionChilds AS $child)
                                            {{--                                        {{ $child }}--}}
                                            {{--                                        <hr>--}}
                                            {{--                                    {{$allPerOfRole}}--}}


                                            <tr class="{{ substr($child->url,0,4) == 'api/' ? 'text-danger' : ''  }}">
                                                <td data-code-pos="ppp1667922763980"
                                                    style="padding-left: 8px!important;">
                                                    <i style="display: inline-block; border: 1px solid #ccc; font-size: smaller; padding: 1px 3px">
                                                    {{$data->name}} &nbsp;
                                                    </i>
                                                    <label title="{{$child->route_name_code}}
                                                        / {{$child->route_name_code}}">
                                                        <input data-role-id="{{@request('id')}}"
                                                               class="checkbox_child_route" name="route_name_code[]"
                                                               {{ $allPerOfRole->contains('route_name_code', $child->route_name_code) ? 'checked' : ''  }}
                                                               value="{{$child->route_name_code}}"
                                                               title="Enable this role for {{$data->name}} : {{$child->route_name_code}} / {{$child->route_name_code}}"
                                                               type="checkbox">
                                                        {{   $child->display_name }}
                                                    </label>
                                                </td>
                                                <td>
                                                    name: {{$child->route_name_code}}
                                                </td>
                                                <td>
                                                    <a href="/{{ explode('/{',$child->url)[0]}}"
                                                       target="_blank">url: {{ $child->url}}</a>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </table>
                                </div>

                            @endforeach
                        </div>


                        <div class="col-md-12">

                        </div>

                    </div>
                </div>
            </section>
        </form>

        <button style="position: fixed; top: 75px; right: 20px" type="text" class="btn btn-primary"
                id="save_all_route">Save Roles
        </button>

        <button title="Show Only Enable" style="position: fixed; top: 120px; right: 20px" type="text" class="btn btn-success"
                onclick="showOnlyEnable()"> +
        </button>

        <?php
        if(isSupperAdmin_()){
        ?>

        <a href="/tool/auto-insert-route-permission">
        <button title="Auto Inser Route" style="position: fixed; top: 180px; right: 20px" type="text"
                class="btn  btn-default">
             .
        </button>
        </a>

        <?php
        }
        ?>


        <!-- /.content -->
    </div>

    <script>
        function showOnlyEnable(){

            $(".inside_a_parent").each(function () {
                let found = $(this).find('.checkbox_child_route').is(':checked');
                if(!found)
                    $(this).toggle()
            });

            $(".checkbox_child_route").each(function (){
                if(!$(this).prop("checked")){
                    $(this).parents('tr').toggle();
                }
            });
            window.scrollTo(0, 0);

        }
    </script>
@endsection


