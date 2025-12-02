<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Gate;

class DemoGate extends Controller
{
    public function index()
    {
        return view('admin.demogate.index');
    }

    public function test1()
    {
        return view('admin.demogate.test1');
    }

    public function test3()
    {

    }

    public function test2()
    {
        if (Gate::allows('is-admin')) {
            return view('admin.demogate.test2');
        } else {
            abort('403');
        }
    }
}
