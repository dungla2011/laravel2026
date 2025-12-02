<!DOCTYPE html>
<html lang="en">
<head>
    <title>Bootstrap 5 Dropdown Example</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</head>
<body>

<?php

    $link = \LadLib\Common\UrlHelper1::getUriWithoutParam();

$options = [
    1 => 'Nhập kho',
    2 => 'Kiểm kê định kỳ',
    3 => 'Thanh lý'
];
if($selectedValue = request()->get('funcs', 0))
    setcookie('funcs', $selectedValue, time() + 3600 * 24 * 30, '/');

if (isset($_COOKIE['funcs'])) {
    $selectedValue = $_COOKIE['funcs'];
}

$funcName = $options[$selectedValue] ?? 'Chưa Chọn chức năng';
?>

<div class="container mt-2">
    <div class="row mb-2">
        <div class="col-xs-8">

            <select onchange="location.href='<?php echo $link ?>?funcs=' + this.value"  class="form-select form-group-sm" id="select_func">
                <option selected>Chọn chức năng</option>
                <?php foreach ($options as $value => $label): ?>
                <option value="<?php echo $value; ?>" <?php echo ($value == $selectedValue) ? 'selected' : ''; ?>>
                        <?php echo $label; ?>
                </option>
                <?php endforeach; ?>
            </select>
        </div>
    </div>


    <?php

    echo "Scan-Time: "  .nowyh_vn() . "<br/>";

    $all = request()->all();

    if($qr = request('qrs')){

        $devices = [
            ['stt' => 1, 'date' => '2023-10-01 10:00:00', 'code' => 'DEV001', 'name' => 'Thiết bị 1'],
            ['stt' => 2, 'date' => '2023-10-02 11:00:00', 'code' => 'DEV002', 'name' => 'Thiết bị 2'],
            ['stt' => 3, 'date' => '2023-10-03 12:00:00', 'code' => 'DEV003', 'name' => 'Thiết bị 3'],
            ['stt' => 4, 'date' => '2023-10-03 12:00:00', 'code' => 'DEV003', 'name' => 'Thiết bị 4'],
            ['stt' => 5, 'date' => '2023-10-03 12:00:00', 'code' => 'DEV003', 'name' => 'Thiết bị 5'],
            ['stt' => 6, 'date' => '2023-10-03 12:00:00', 'code' => 'DEV003', 'name' => 'Thiết bị 6'],
            // Add more devices as needed
        ];

        ?>

    <div class="my-2">
        <b >Danh sách thiết bị đã {{$funcName}}</b>
    </div>
        <table class="table table-bordered table-sm mt-2">
            <thead>
            <tr>
                <th>STT</th>
                <th>Ngày giờ</th>
                <th>Mã số</th>
                <th>Tên</th>
            </tr>
            </thead>
            <tbody>
                <?php foreach ($devices as $device): ?>
            <tr>
                <td><?php echo $device['stt']; ?></td>
                <td><?php echo $device['date']; ?></td>
                <td><?php echo $device['code']; ?></td>
                <td><?php echo $device['name']; ?></td>
            </tr>
            <?php endforeach; ?>
            </tbody>
        </table>


    <?php


    }


    if($code = request('qrs')){
        echo "<div class='my-2'>CODE: $code</div>";
    }

//    echo "<pre> >>> " . __FILE__ . "(" . __LINE__ . ")<br/>";
//    print_r($all);
//    echo "</pre>";

    ?>

</div>



</body>
</html>
