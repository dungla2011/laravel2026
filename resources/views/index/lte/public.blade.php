<?php

use LadLib\Common\Database\MetaClassCommon;

?>

@extends("layouts_multi.lte")

@section("title")
    Admin Index
@endsection

@section('header')
    @include('parts.header-all')
@endsection

@section('css')
    <link rel="stylesheet" href="/vendor/div_table2/div_table2.css?v=<?php echo filemtime(public_path().'/vendor/div_table2/div_table2.css'); ?>">
    <link rel="stylesheet" href="/admins/table_mng.css?v=<?php echo filemtime(public_path().'/admins/table_mng.css'); ?>">
@endsection

@section('js')
    <script src="/admins/table_mng.js"></script>
    <script src="/vendor/div_table2/div_table2.js"></script>
    <script src="/admins/meta-data-table/meta-data-table.js"></script>

@endsection

@section("content")

    <div class="content-wrapper">
        <!-- Content Header (Page header) -->
        <div class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1 class="m-0" data-code-pos="ppp1679476845648">DASHBOARD</h1>
                    </div><!-- /.col -->
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item" ppp08579374590958><a href="#">Admin</a></li>
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
                <div class="container-fluid">


                    <div class="row">

                        <div class="col-md-8">


                            <div class="row">


                                <div class="col-md-6">

                                    <div class="card">
                                        <div class="card-header">
                                            <h3 class="card-title">Latest Members</h3>
                                            <div class="card-tools">
                                                <span class="badge badge-danger">8 New Members</span>
                                                <button type="button" class="btn btn-tool" data-card-widget="collapse">
                                                    <i class="fas fa-minus"></i>
                                                </button>
                                                <button type="button" class="btn btn-tool" data-card-widget="remove">
                                                    <i class="fas fa-times"></i>
                                                </button>
                                            </div>
                                        </div>

                                        <div class="card-body p-0">
                                            <ul class="users-list clearfix">
                                                <li>
                                                    <img src="/adminlte/dist/img/user1-128x128.jpg" alt="User Image">
                                                    <a class="users-list-name" href="#">Alexander Pierce</a>
                                                    <span class="users-list-date">Today</span>
                                                </li>
                                                <li>
                                                    <img src="/adminlte/dist/img/user8-128x128.jpg" alt="User Image">
                                                    <a class="users-list-name" href="#">Norman</a>
                                                    <span class="users-list-date">Yesterday</span>
                                                </li>
                                                <li>
                                                    <img src="/adminlte/dist/img/user7-128x128.jpg" alt="User Image">
                                                    <a class="users-list-name" href="#">Jane</a>
                                                    <span class="users-list-date">12 Jan</span>
                                                </li>
                                                <li>
                                                    <img src="/adminlte/dist/img/user6-128x128.jpg" alt="User Image">
                                                    <a class="users-list-name" href="#">John</a>
                                                    <span class="users-list-date">12 Jan</span>
                                                </li>
                                                <li>
                                                    <img src="/adminlte/dist/img/user2-160x160.jpg" alt="User Image">
                                                    <a class="users-list-name" href="#">Alexander</a>
                                                    <span class="users-list-date">13 Jan</span>
                                                </li>
                                                <li>
                                                    <img src="/adminlte/dist/img/user5-128x128.jpg" alt="User Image">
                                                    <a class="users-list-name" href="#">Sarah</a>
                                                    <span class="users-list-date">14 Jan</span>
                                                </li>
                                                <li>
                                                    <img src="/adminlte/dist/img/user4-128x128.jpg" alt="User Image">
                                                    <a class="users-list-name" href="#">Nora</a>
                                                    <span class="users-list-date">15 Jan</span>
                                                </li>
                                                <li>
                                                    <img src="/adminlte/dist/img/user3-128x128.jpg" alt="User Image">
                                                    <a class="users-list-name" href="#">Nadia</a>
                                                    <span class="users-list-date">15 Jan</span>
                                                </li>
                                            </ul>

                                        </div>

                                        <div class="card-footer text-center">
                                            <a href="javascript:">View All Users</a>
                                        </div>

                                    </div>

                                </div>
                                <div class="col-md-6">

                                    <div class="card direct-chat direct-chat-warning">
                                        <div class="card-header">
                                            <h3 class="card-title">Direct Chat</h3>
                                            <div class="card-tools">
                                                <span title="3 New Messages" class="badge badge-warning">3</span>
                                                <button type="button" class="btn btn-tool" data-card-widget="collapse">
                                                    <i class="fas fa-minus"></i>
                                                </button>
                                                <button type="button" class="btn btn-tool" title="Contacts"
                                                        data-widget="chat-pane-toggle">
                                                    <i class="fas fa-comments"></i>
                                                </button>
                                                <button type="button" class="btn btn-tool" data-card-widget="remove">
                                                    <i class="fas fa-times"></i>
                                                </button>
                                            </div>
                                        </div>

                                        <div class="card-body">

                                            <div class="direct-chat-messages">

                                                <div class="direct-chat-msg">
                                                    <div class="direct-chat-infos clearfix">
                                                        <span
                                                            class="direct-chat-name float-left">Alexander Pierce</span>
                                                        <span
                                                            class="direct-chat-timestamp float-right">23 Jan 2:00 pm</span>
                                                    </div>

                                                    <img class="direct-chat-img" src="/adminlte/dist/img/user1-128x128.jpg"
                                                         alt="message user image">

                                                    <div class="direct-chat-text">
                                                        Is this template really for free? That's unbelievable!
                                                    </div>

                                                </div>


                                                <div class="direct-chat-msg right">
                                                    <div class="direct-chat-infos clearfix">
                                                        <span class="direct-chat-name float-right">Sarah Bullock</span>
                                                        <span
                                                            class="direct-chat-timestamp float-left">23 Jan 2:05 pm</span>
                                                    </div>

                                                    <img class="direct-chat-img" src="/adminlte/dist/img/user3-128x128.jpg"
                                                         alt="message user image">

                                                    <div class="direct-chat-text">
                                                        You better believe it!
                                                    </div>

                                                </div>


                                                <div class="direct-chat-msg">
                                                    <div class="direct-chat-infos clearfix">
                                                        <span
                                                            class="direct-chat-name float-left">Alexander Pierce</span>
                                                        <span
                                                            class="direct-chat-timestamp float-right">23 Jan 5:37 pm</span>
                                                    </div>

                                                    <img class="direct-chat-img" src="/adminlte/dist/img/user1-128x128.jpg"
                                                         alt="message user image">

                                                    <div class="direct-chat-text">
                                                        Working with AdminLTE on a great new app! Wanna join?
                                                    </div>

                                                </div>


                                                <div class="direct-chat-msg right">
                                                    <div class="direct-chat-infos clearfix">
                                                        <span class="direct-chat-name float-right">Sarah Bullock</span>
                                                        <span
                                                            class="direct-chat-timestamp float-left">23 Jan 6:10 pm</span>
                                                    </div>

                                                    <img class="direct-chat-img" src="/adminlte/dist/img/user3-128x128.jpg"
                                                         alt="message user image">

                                                    <div class="direct-chat-text">
                                                        I would love to.
                                                    </div>

                                                </div>

                                            </div>


                                            <div class="direct-chat-contacts">
                                                <ul class="contacts-list">
                                                    <li>
                                                        <a href="#">
                                                            <img class="contacts-list-img"
                                                                 src="/adminlte/dist/img/user1-128x128.jpg" alt="User Avatar">
                                                            <div class="contacts-list-info">
<span class="contacts-list-name">
Count Dracula
<small class="contacts-list-date float-right">2/28/2015</small>
</span>
                                                                <span class="contacts-list-msg">How have you been? I was...</span>
                                                            </div>

                                                        </a>
                                                    </li>

                                                    <li>
                                                        <a href="#">
                                                            <img class="contacts-list-img"
                                                                 src="/adminlte/dist/img/user7-128x128.jpg" alt="User Avatar">
                                                            <div class="contacts-list-info">
<span class="contacts-list-name">
Sarah Doe
<small class="contacts-list-date float-right">2/23/2015</small>
</span>
                                                                <span
                                                                    class="contacts-list-msg">I will be waiting for...</span>
                                                            </div>

                                                        </a>
                                                    </li>

                                                    <li>
                                                        <a href="#">
                                                            <img class="contacts-list-img"
                                                                 src="/adminlte/dist/img/user3-128x128.jpg" alt="User Avatar">
                                                            <div class="contacts-list-info">
<span class="contacts-list-name">
Nadia Jolie
<small class="contacts-list-date float-right">2/20/2015</small>
</span>
                                                                <span
                                                                    class="contacts-list-msg">I'll call you back at...</span>
                                                            </div>

                                                        </a>
                                                    </li>

                                                    <li>
                                                        <a href="#">
                                                            <img class="contacts-list-img"
                                                                 src="/adminlte/dist/img/user5-128x128.jpg" alt="User Avatar">
                                                            <div class="contacts-list-info">
<span class="contacts-list-name">
Nora S. Vans
<small class="contacts-list-date float-right">2/10/2015</small>
</span>
                                                                <span
                                                                    class="contacts-list-msg">Where is your new...</span>
                                                            </div>

                                                        </a>
                                                    </li>

                                                    <li>
                                                        <a href="#">
                                                            <img class="contacts-list-img"
                                                                 src="/adminlte/dist/img/user6-128x128.jpg" alt="User Avatar">
                                                            <div class="contacts-list-info">
<span class="contacts-list-name">
John K.
<small class="contacts-list-date float-right">1/27/2015</small>
</span>
                                                                <span
                                                                    class="contacts-list-msg">Can I take a look at...</span>
                                                            </div>

                                                        </a>
                                                    </li>

                                                    <li>
                                                        <a href="#">
                                                            <img class="contacts-list-img"
                                                                 src="/adminlte/dist/img/user8-128x128.jpg" alt="User Avatar">
                                                            <div class="contacts-list-info">
<span class="contacts-list-name">
Kenneth M.
<small class="contacts-list-date float-right">1/4/2015</small>
</span>
                                                                <span
                                                                    class="contacts-list-msg">Never mind I found...</span>
                                                            </div>

                                                        </a>
                                                    </li>

                                                </ul>

                                            </div>

                                        </div>

                                        <div class="card-footer">
                                            <form action="#" method="post">
                                                <div class="input-group">
                                                    <input type="text" name="message" placeholder="Type Message ..."
                                                           class="form-control">
                                                    <span class="input-group-append">
<button type="button" class="btn btn-warning">Send</button>
</span>
                                                </div>
                                            </form>
                                        </div>

                                    </div>

                                </div>
                            </div>


                            <div class="card">
                                <div class="card-header border-transparent">
                                    <h3 class="card-title">Latest Orders</h3>
                                    <div class="card-tools">
                                        <button type="button" class="btn btn-tool" data-card-widget="collapse">
                                            <i class="fas fa-minus"></i>
                                        </button>
                                        <button type="button" class="btn btn-tool" data-card-widget="remove">
                                            <i class="fas fa-times"></i>
                                        </button>
                                    </div>
                                </div>

                                <div class="card-body p-0">
                                    <div class="table-responsive">
                                        <table class="table m-0">
                                            <thead>
                                            <tr>
                                                <th>Order ID</th>
                                                <th>Item</th>
                                                <th>Status</th>
                                                <th>Popularity</th>
                                            </tr>
                                            </thead>
                                            <tbody>
                                            <tr>
                                                <td><a href="pages/examples/invoice.html">OR9842</a></td>
                                                <td>Call of Duty IV</td>
                                                <td><span class="badge badge-success">Shipped</span></td>
                                                <td>
                                                    <div class="sparkbar" data-color="#00a65a" data-height="20">
                                                        90,80,90,-70,61,-83,63
                                                    </div>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td><a href="pages/examples/invoice.html">OR1848</a></td>
                                                <td>Samsung Smart TV</td>
                                                <td><span class="badge badge-warning">Pending</span></td>
                                                <td>
                                                    <div class="sparkbar" data-color="#f39c12" data-height="20">
                                                        90,80,-90,70,61,-83,68
                                                    </div>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td><a href="pages/examples/invoice.html">OR7429</a></td>
                                                <td>iPhone 6 Plus</td>
                                                <td><span class="badge badge-danger">Delivered</span></td>
                                                <td>
                                                    <div class="sparkbar" data-color="#f56954" data-height="20">
                                                        90,-80,90,70,-61,83,63
                                                    </div>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td><a href="pages/examples/invoice.html">OR7429</a></td>
                                                <td>Samsung Smart TV</td>
                                                <td><span class="badge badge-info">Processing</span></td>
                                                <td>
                                                    <div class="sparkbar" data-color="#00c0ef" data-height="20">
                                                        90,80,-90,70,-61,83,63
                                                    </div>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td><a href="pages/examples/invoice.html">OR1848</a></td>
                                                <td>Samsung Smart TV</td>
                                                <td><span class="badge badge-warning">Pending</span></td>
                                                <td>
                                                    <div class="sparkbar" data-color="#f39c12" data-height="20">
                                                        90,80,-90,70,61,-83,68
                                                    </div>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td><a href="pages/examples/invoice.html">OR7429</a></td>
                                                <td>iPhone 6 Plus</td>
                                                <td><span class="badge badge-danger">Delivered</span></td>
                                                <td>
                                                    <div class="sparkbar" data-color="#f56954" data-height="20">
                                                        90,-80,90,70,-61,83,63
                                                    </div>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td><a href="pages/examples/invoice.html">OR9842</a></td>
                                                <td>Call of Duty IV</td>
                                                <td><span class="badge badge-success">Shipped</span></td>
                                                <td>
                                                    <div class="sparkbar" data-color="#00a65a" data-height="20">
                                                        90,80,90,-70,61,-83,63
                                                    </div>
                                                </td>
                                            </tr>
                                            </tbody>
                                        </table>
                                    </div>

                                </div>

                                <div class="card-footer clearfix">
                                    <a href="javascript:void(0)" class="btn btn-sm btn-info float-left">Place New
                                        Order</a>
                                    <a href="javascript:void(0)" class="btn btn-sm btn-secondary float-right">View All
                                        Orders</a>
                                </div>

                            </div>

                        </div>

                        <div class="col-md-4">

                            <div class="info-box mb-3 bg-warning">
                                <span class="info-box-icon"><i class="fas fa-tag"></i></span>
                                <div class="info-box-content">
                                    <span class="info-box-text">Inventory</span>
                                    <span class="info-box-number">5,200</span>
                                </div>

                            </div>

                            <div class="info-box mb-3 bg-success">
                                <span class="info-box-icon"><i class="far fa-heart"></i></span>
                                <div class="info-box-content">
                                    <span class="info-box-text">Mentions</span>
                                    <span class="info-box-number">92,050</span>
                                </div>

                            </div>

                            <div class="info-box mb-3 bg-danger">
                                <span class="info-box-icon"><i class="fas fa-cloud-download-alt"></i></span>
                                <div class="info-box-content">
                                    <span class="info-box-text">Downloads</span>
                                    <span class="info-box-number">114,381</span>
                                </div>

                            </div>

                            <div class="info-box mb-3 bg-info">
                                <span class="info-box-icon"><i class="far fa-comment"></i></span>
                                <div class="info-box-content">
                                    <span class="info-box-text">Direct Messages</span>
                                    <span class="info-box-number">163,921</span>
                                </div>

                            </div>

                            <div class="card">
                                <div class="card-header">
                                    <h3 class="card-title">Browser Usage</h3>
                                    <div class="card-tools">
                                        <button type="button" class="btn btn-tool" data-card-widget="collapse">
                                            <i class="fas fa-minus"></i>
                                        </button>
                                        <button type="button" class="btn btn-tool" data-card-widget="remove">
                                            <i class="fas fa-times"></i>
                                        </button>
                                    </div>
                                </div>

                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-8">
                                            <div class="chart-responsive">
                                                <div class="chartjs-size-monitor">
                                                    <div class="chartjs-size-monitor-expand">
                                                        <div class=""></div>
                                                    </div>
                                                    <div class="chartjs-size-monitor-shrink">
                                                        <div class=""></div>
                                                    </div>
                                                </div>
                                                <canvas id="pieChart" height="120"
                                                        style="display: block; width: 241px; height: 120px;" width="241"
                                                        class="chartjs-render-monitor"></canvas>
                                            </div>

                                        </div>

                                        <div class="col-md-4">
                                            <ul class="chart-legend clearfix">
                                                <li><i class="far fa-circle text-danger"></i> Chrome</li>
                                                <li><i class="far fa-circle text-success"></i> IE</li>
                                                <li><i class="far fa-circle text-warning"></i> FireFox</li>
                                                <li><i class="far fa-circle text-info"></i> Safari</li>
                                                <li><i class="far fa-circle text-primary"></i> Opera</li>
                                                <li><i class="far fa-circle text-secondary"></i> Navigator</li>
                                            </ul>
                                        </div>

                                    </div>

                                </div>

                                <div class="card-footer p-0">
                                    <ul class="nav nav-pills flex-column">
                                        <li class="nav-item">
                                            <a href="#" class="nav-link">
                                                United States of America
                                                <span class="float-right text-danger">
<i class="fas fa-arrow-down text-sm"></i>
12%</span>
                                            </a>
                                        </li>
                                        <li class="nav-item">
                                            <a href="#" class="nav-link">
                                                India
                                                <span class="float-right text-success">
<i class="fas fa-arrow-up text-sm"></i> 4%
</span>
                                            </a>
                                        </li>
                                        <li class="nav-item">
                                            <a href="#" class="nav-link">
                                                China
                                                <span class="float-right text-warning">
<i class="fas fa-arrow-left text-sm"></i> 0%
</span>
                                            </a>
                                        </li>
                                    </ul>
                                </div>

                            </div>


                            <div class="card">
                                <div class="card-header">
                                    <h3 class="card-title">Recently Added Products</h3>
                                    <div class="card-tools">
                                        <button type="button" class="btn btn-tool" data-card-widget="collapse">
                                            <i class="fas fa-minus"></i>
                                        </button>
                                        <button type="button" class="btn btn-tool" data-card-widget="remove">
                                            <i class="fas fa-times"></i>
                                        </button>
                                    </div>
                                </div>

                                <div class="card-body p-0">
                                    <ul class="products-list product-list-in-card pl-2 pr-2">
                                        <li class="item">
                                            <div class="product-img">
                                                <img src="/adminlte/dist/img/default-150x150.png" alt="Product Image"
                                                     class="img-size-50">
                                            </div>
                                            <div class="product-info">
                                                <a href="javascript:void(0)" class="product-title">Samsung TV
                                                    <span class="badge badge-warning float-right">$1800</span></a>
                                                <span class="product-description">
Samsung 32" 1080p 60Hz LED Smart HDTV.
</span>
                                            </div>
                                        </li>

                                        <li class="item">
                                            <div class="product-img">
                                                <img src="/adminlte/dist/img/default-150x150.png" alt="Product Image"
                                                     class="img-size-50">
                                            </div>
                                            <div class="product-info">
                                                <a href="javascript:void(0)" class="product-title">Bicycle
                                                    <span class="badge badge-info float-right">$700</span></a>
                                                <span class="product-description">
26" Mongoose Dolomite Men's 7-speed, Navy Blue.
</span>
                                            </div>
                                        </li>

                                        <li class="item">
                                            <div class="product-img">
                                                <img src="/adminlte/dist/img/default-150x150.png" alt="Product Image"
                                                     class="img-size-50">
                                            </div>
                                            <div class="product-info">
                                                <a href="javascript:void(0)" class="product-title">
                                                    Xbox One <span class="badge badge-danger float-right">
$350
</span>
                                                </a>
                                                <span class="product-description">
Xbox One Console Bundle with Halo Master Chief Collection.
</span>
                                            </div>
                                        </li>

                                        <li class="item">
                                            <div class="product-img">
                                                <img src="/adminlte/dist/img/default-150x150.png" alt="Product Image"
                                                     class="img-size-50">
                                            </div>
                                            <div class="product-info">
                                                <a href="javascript:void(0)" class="product-title">PlayStation 4
                                                    <span class="badge badge-success float-right">$399</span></a>
                                                <span class="product-description">
PlayStation 4 500GB Console (PS4)
</span>
                                            </div>
                                        </li>

                                    </ul>
                                </div>

                                <div class="card-footer text-center">
                                    <a href="javascript:void(0)" class="uppercase">View All Products</a>
                                </div>

                            </div>

                        </div>

                    </div>

                </div>


            </div>
        </section>
        <!-- /.content -->
    </div>
@endsection
