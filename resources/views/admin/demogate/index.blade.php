@extends("layouts.adm")

@section("title")
    Demo gate
@endsection

@section('header')
    @include('parts.header-all')
@endsection

@section("content")

    @include("admin.demogate.common_gate")

@endsection
