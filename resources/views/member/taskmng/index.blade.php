<?php
use LadLib\Common\Database\MetaClassCommon;
?>
@extends("layouts.member")

@section("title")
    Member
@endsection

@section('header')
    @include('parts.header-all')
@endsection

@section('css')
    <link rel="stylesheet" href="/vendor/div_table2/div_table2.css?v=<?php echo filemtime(public_path().'/vendor/div_table2/div_table2.css'); ?>">
    <link rel="stylesheet" href="/admins/table_mng.css?v=<?php echo filemtime(public_path().'/admins/table_mng.css'); ?>">
    <style>
        canvas {
            -moz-user-select: none;
            -webkit-user-select: none;
            -ms-user-select: none;
        }
    </style>
@endsection

@section('js')
    <script src="/admins/table_mng.js"></script>
    <script src="/vendor/div_table2/div_table2.js"></script>
    <script src="/admins/meta-data-table/meta-data-table.js"></script>

    <script src="https://www.chartjs.org/dist/2.6.0/Chart.bundle.js"></script>
    <script src="https://www.chartjs.org/samples/2.6.0/utils.js"></script>


    <script>
        $("#btn-show-token").on('click', function (){
            $("#user_token").toggle();
        })
    </script>

    <script>
        var randomScalingFactor = function() {
            return Math.round(Math.random() * 100);
        };

        var config = {
            type: 'doughnut',
            data: {
                datasets: [{
                    data: [
                        randomScalingFactor(),
                        randomScalingFactor(),
                        randomScalingFactor(),
                        randomScalingFactor(),
                        randomScalingFactor(),
                    ],
                    backgroundColor: [
                        window.chartColors.red,
                        window.chartColors.orange,
                        window.chartColors.yellow,
                        window.chartColors.green,
                        window.chartColors.blue,
                    ],
                    label: 'Dataset 1'
                }],
                labels: [
                    "Red",
                    "Orange",
                    "Yellow",
                    "Green",
                    "Blue"
                ]
            },
            options: {
                responsive: true,
                legend: {
                    position: 'top',
                },
                title: {
                    display: true,
                    text: 'Chart.js Doughnut Chart'
                },
                animation: {
                    animateScale: true,
                    animateRotate: true
                }
            }
        };

        window.onload = function() {
            var ctx = document.getElementById("chart-area").getContext("2d");
            window.myDoughnut = new Chart(ctx, config);
        };

        document.getElementById('randomizeData').addEventListener('click', function() {
            config.data.datasets.forEach(function(dataset) {
                dataset.data = dataset.data.map(function() {
                    return randomScalingFactor();
                });
            });

            window.myDoughnut.update();
        });

        var colorNames = Object.keys(window.chartColors);
        document.getElementById('addDataset').addEventListener('click', function() {
            var newDataset = {
                backgroundColor: [],
                data: [],
                label: 'New dataset ' + config.data.datasets.length,
            };

            for (var index = 0; index < config.data.labels.length; ++index) {
                newDataset.data.push(randomScalingFactor());

                var colorName = colorNames[index % colorNames.length];;
                var newColor = window.chartColors[colorName];
                newDataset.backgroundColor.push(newColor);
            }

            config.data.datasets.push(newDataset);
            window.myDoughnut.update();
        });

        document.getElementById('addData').addEventListener('click', function() {
            if (config.data.datasets.length > 0) {
                config.data.labels.push('data #' + config.data.labels.length);

                var colorName = colorNames[config.data.datasets[0].data.length % colorNames.length];;
                var newColor = window.chartColors[colorName];

                config.data.datasets.forEach(function(dataset) {
                    dataset.data.push(randomScalingFactor());
                    dataset.backgroundColor.push(newColor);
                });

                window.myDoughnut.update();
            }
        });

        document.getElementById('removeDataset').addEventListener('click', function() {
            config.data.datasets.splice(0, 1);
            window.myDoughnut.update();
        });

        document.getElementById('removeData').addEventListener('click', function() {
            config.data.labels.splice(-1, 1); // remove the label first

            config.data.datasets.forEach(function(dataset) {
                dataset.data.pop();
                dataset.backgroundColor.pop();
            });

            window.myDoughnut.update();
        });
    </script>

    <script>
        var timeFormat = 'MM/DD/YYYY HH:mm';

        function newDate(days) {
            return moment().add(days, 'd').toDate();
        }

        function newDateString(days) {
            return moment().add(days, 'd').format(timeFormat);
        }

        function newTimestamp(days) {
            return moment().add(days, 'd').unix();
        }

        var color = Chart.helpers.color;
        var config = {
            type: 'line',
            data: {
                labels: [ // Date Objects
                    newDate(0),
                    newDate(1),
                    newDate(2),
                    newDate(3),
                    newDate(4),
                    newDate(5),
                    newDate(6)
                ],
                datasets: [{
                    label: "My First dataset",
                    backgroundColor: color(window.chartColors.red).alpha(0.5).rgbString(),
                    borderColor: window.chartColors.red,
                    fill: false,
                    data: [
                        randomScalingFactor(),
                        randomScalingFactor(),
                        randomScalingFactor(),
                        randomScalingFactor(),
                        randomScalingFactor(),
                        randomScalingFactor(),
                        randomScalingFactor()
                    ],
                }, {
                    label: "My Second dataset",
                    backgroundColor: color(window.chartColors.blue).alpha(0.5).rgbString(),
                    borderColor: window.chartColors.blue,
                    fill: false,
                    data: [
                        randomScalingFactor(),
                        randomScalingFactor(),
                        randomScalingFactor(),
                        randomScalingFactor(),
                        randomScalingFactor(),
                        randomScalingFactor(),
                        randomScalingFactor()
                    ],
                }, {
                    label: "Dataset with point data",
                    backgroundColor: color(window.chartColors.green).alpha(0.5).rgbString(),
                    borderColor: window.chartColors.green,
                    fill: false,
                    data: [{
                        x: newDateString(0),
                        y: randomScalingFactor()
                    }, {
                        x: newDateString(5),
                        y: randomScalingFactor()
                    }, {
                        x: newDateString(7),
                        y: randomScalingFactor()
                    }, {
                        x: newDateString(15),
                        y: randomScalingFactor()
                    }],
                }]
            },
            options: {
                title:{
                    text: "Chart.js Time Scale"
                },
                scales: {
                    xAxes: [{
                        type: "time",
                        time: {
                            format: timeFormat,
                            // round: 'day'
                            tooltipFormat: 'll HH:mm'
                        },
                        scaleLabel: {
                            display: true,
                            labelString: 'Date'
                        }
                    }, ],
                    yAxes: [{
                        scaleLabel: {
                            display: true,
                            labelString: 'value'
                        }
                    }]
                },
            }
        };

        window.onload = function() {
            var ctx = document.getElementById("canvas").getContext("2d");
            window.myLine = new Chart(ctx, config);

        };

        document.getElementById('randomizeData').addEventListener('click', function() {
            config.data.datasets.forEach(function(dataset) {
                dataset.data.forEach(function(dataObj, j) {
                    if (typeof dataObj === 'object') {
                        dataObj.y = randomScalingFactor();
                    } else {
                        dataset.data[j] = randomScalingFactor();
                    }
                });
            });

            window.myLine.update();
        });

        var colorNames = Object.keys(window.chartColors);
        document.getElementById('addDataset').addEventListener('click', function() {
            var colorName = colorNames[config.data.datasets.length % colorNames.length];
            var newColor = window.chartColors[colorName]
            var newDataset = {
                label: 'Dataset ' + config.data.datasets.length,
                borderColor: newColor,
                backgroundColor: color(newColor).alpha(0.5).rgbString(),
                data: [],
            };

            for (var index = 0; index < config.data.labels.length; ++index) {
                newDataset.data.push(randomScalingFactor());
            }

            config.data.datasets.push(newDataset);
            window.myLine.update();
        });

        document.getElementById('addData').addEventListener('click', function() {
            if (config.data.datasets.length > 0) {
                config.data.labels.push(newDate(config.data.labels.length));

                for (var index = 0; index < config.data.datasets.length; ++index) {
                    if (typeof config.data.datasets[index].data[0] === "object") {
                        config.data.datasets[index].data.push({
                            x: newDate(config.data.datasets[index].data.length),
                            y: randomScalingFactor(),
                        });
                    } else {
                        config.data.datasets[index].data.push(randomScalingFactor());
                    }
                }

                window.myLine.update();
            }
        });

        document.getElementById('removeDataset').addEventListener('click', function() {
            config.data.datasets.splice(0, 1);
            window.myLine.update();
        });

        document.getElementById('removeData').addEventListener('click', function() {
            config.data.labels.splice(-1, 1); // remove the label first

            config.data.datasets.forEach(function(dataset, datasetIndex) {
                dataset.data.pop();
            });

            window.myLine.update();
        });
    </script>

@endsection

@section("content")
    <?php

    $user = \Illuminate\Support\Facades\Auth::user();

    ?>

    <div class="content-wrapper">
        <!-- Content Header (Page header) -->

        <!-- Main content -->
        <section class="content">
            <div class="container-fluid pt-3">

                <div class="sec1"
                     style="">
                    <div class="row">
                        <div class="col-sm-4">

                            <i class="fa fa-fw fa-user"></i>
                            Mã Tài khoản:


                            <?php
                            $ms = \App\Components\ClassRandId2::getRandFromId(getUserIdCurrent_());
                            echo "<b> $ms </b>";
                            if (\App\Models\User::isSupperAdmin()) {
                                echo " <span style='color: transparent'> [" . getUserIdCurrent_() . '] </span>';
                            }


                            //Các role của user
                            echo "<br> <i class='fa fa-fw fa-check-square'></i> Quyền Tài khoản: <b> " . $user->getRoleNames() . "</b> ";


                            ?>
                        </div>
                        <div class="col-sm-6">
                        <span>
                            <?php
                            echo "  <i class='fa fa-fw fa-inbox'></i> " . $user->email . " , " . $user->username;
                            if (!$user->password)
                                echo "<br/>\n <a href='/reset-password'>
                                <i class='fa fa-fw fa-unlock-alt'></i>
                                 Đặt mật khẩu
                                 </a>";
                            else
                                echo "<br/>\n <i class='fa fa-fw fa-lock'></i> <a href='/member/set-password'> Đặt mật khẩu </a>";
                            ?>
                        </span>
                        </div>
                        <div class="col-sm-2">
                            <div class="float-end">
                        <span id="user_token" style="display: none">
                            <input readonly
                                   style=""
                                   type="text" class="form-control form-control-sm" value="<?php
                            echo Auth()->user()->getJWTUserToken() ;
                            ?>">
                            <?php
                            ?>
                        </span>
                                <button id="btn-show-token" style="display: inline-block" type="button"
                                        class="btn btn-sm btn-default">
                                    <i class="fa fw fa-cog"></i>
                                    Get Api Token
                                </button>

                            </div>
                        </div>
                    </div>
                    <?php

                    //Lấy thông tin Deparement name của user
                    $depName = \App\Models\EventInfo::getDepartmentIdOfUser($user->id, 1)?->name ?? " <b> Chưa xác định </b> -
                     Bạn cần Liên hệ Admin để gán tài khoản vào một Phòng ban,
                     và có thể thao tác nội dung các Sự kiện của phòng ban.";
                    $depId = \App\Models\EventInfo::getDepartmentIdOfUser($user->id, 1)?->id ?? -10000;

//                echo "\n<b> <i class='fa fa-fw fa-check'></i> Bạn thuộc đơn vị: $depName </b>";

                    //Các thành viên có quyền quản trị, là các user trong bảng Department_User
                    $adminUsers = \App\Models\DepartmentUser::where('department_id', $depId)->get();
                    $adminUserIds = $adminUsers->pluck('user_id')->toArray();
                    //Lấy ra userObj  tư mảng naày
                    $adminUserObjs = \App\Models\User::whereIn('id', $adminUserIds)->get();
//                echo "<br/>\n <br/><i class='fa fa-fw fa-check'></i> Danh sách các thành viên có quyền Quản trị Sự kiện: ";
                    $cc = 0;

                    $userList = "<table class='table table-bordered mx-2 mt-2'>
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Name</th>
                        <th>Email</th>
                    </tr>
                </thead>
                <tbody>";

                    $cc = 0;
                    foreach ($adminUserObjs as $adminUserObj) {
                        $cc++;
                        $name = $adminUserObj->name ?? '';
                        $userList .= "<tr>
                    <td>$cc</td>
                    <td>$name</td>
                    <td>$adminUserObj->email</td>
                  </tr>";
                    }

                    if ($cc == 0) {
                        $userList .= "<tr><td colspan='3'>Không có</td></tr>";
                    }

                    $userList .= "</tbody></table>";
                    ?>

                </div>


                <div class="sec1"
                     style="">
                    <div class="row">
                        <div class="col-sm-3">
                            <b>
                            Thống kê Công việc Bạn Chủ trì
                            </b>
                            <br><br>

                            <div id="canvas-holder" style="">
                                <canvas id="chart-area" />
                            </div>
{{--                            <button id="randomizeData">Randomize Data</button>--}}
{{--                            <button id="addDataset">Add Dataset</button>--}}
{{--                            <button id="removeDataset">Remove Dataset</button>--}}
{{--                            <button id="addData">Add Data</button>--}}
{{--                            <button id="removeData">Remove Data</button>--}}

                        </div>
                    </div>
                </div>

                <div class="sec1"
                     style="">
                    <div class="row">
                        <div class="col-sm-4">
                            <b>
                            Thống kê Công việc Bạn được giao
                            </b>


                        </div>
                    </div>
                </div>

            </div>
        </section>
        <!-- /.content -->
    </div>
@endsection
