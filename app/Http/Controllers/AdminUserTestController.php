<?php

namespace App\Http\Controllers;

class AdminUserTestController extends Controller
{
    public function __construct()
    {
        parent::__construct();
    }

    public function create()
    {
        return view('admin.demo.add');
    }

    public function index()
    {
        $data = $this->data->latest()->paginate(5);

        return view('admin.demo.index', compact('data'));
    }

    public function edit($id)
    {
        $data = $this->data->find($id);

        return view('admin.demo.edit', compact('data'));
    }
}
