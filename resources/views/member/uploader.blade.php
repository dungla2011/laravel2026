<?php
$us = getCurrentUserId();
if (!$us) {
    bl("Bạn cần đăng nhập để xem trang này", "/login");
    return;
}
?>


@extends("layouts.member")

@section("title")
    Member
@endsection

@section('header')
    @include('parts.header-all')
@endsection

@section('css')
    <link rel="stylesheet"
          href="/vendor/div_table2/div_table2.css?v=<?php echo filemtime(public_path().'/vendor/div_table2/div_table2.css'); ?>">
    <link rel="stylesheet"
          href="/admins/table_mng.css?v=<?php echo filemtime(public_path().'/admins/table_mng.css'); ?>">
    <link rel="stylesheet"
          href="/admins/admin_common.css?v=<?php echo filemtime(public_path().'/admins/admin_common.css'); ?>">

@endsection

@section('js')
    <script src="/admins/table_mng.js"></script>
    <script src="/vendor/div_table2/div_table2.js"></script>
    <script src="/admins/meta-data-table/meta-data-table.js"></script>

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="/admins/admin_logs.js"></script>

    <script>
        $("#btn-show-token").on('click', function () {
            $("#user_token").toggle();
        })


        renderChart((<?php echo \App\Components\U4sHelper::getDownloadStats('user_id_file') ?>));
        renderMonthChart((<?php echo \App\Components\U4sHelper::getMonthDownloadStats('user_id_file') ?>));

        renderChartUpload(<?php echo \App\Components\U4sHelper::getUploadStats() ?>);

                // Tính tổng khi click checkbox
        $(document).on('change', '.check_to_sum', function() {
            let total = 0;
            let selectedMonths = [];

            $('.check_to_sum:checked').each(function() {
                total += parseFloat($(this).val()) || 0;
                // Lấy tháng từ cột thứ 2 của dòng hiện tại
                let month = $(this).closest('tr').find('td:nth-child(2)').text().trim();
                selectedMonths.push(month);
            });

            // Format số tiền với dấu phân cách hàng nghìn
            let formattedTotal = new Intl.NumberFormat('vi-VN').format(total);
            let monthsText = selectedMonths.length > 0 ? 'Các tháng: ' + selectedMonths.join(', ') : '';
            $('.total_sum').html('<b><?php echo getCurrentUserEmail() ?> </b> , Tổng số: ' + formattedTotal + ' VND</b><br>' + monthsText);
        });

    </script>
@endsection

@section("content")

    <div class="content-wrapper ">
        <!-- Content Header (Page header) -->

        <!-- Main content -->
        <div class="content ">

            <div class="container-fluid pt-3">

                    <div class="sec1">
                        <?php
                        $downloadsByMonth = \App\Components\U4sHelper::getMonthDownloadStats('user_id_file', 1, 12);

                        //Đảo ngược Collection
                        $downloadsByMonth = $downloadsByMonth->reverse();


                        $uploaderRate = \App\Models\UploaderInfo::where('user_id', getCurrentUserId())->first()?->rate ?? 2;

                        //Tinh tong tien thuong
                        $totalMoney = 0;
                        foreach ($downloadsByMonth as $row) {
                            $num = 1000 * 1000 * round($uploaderRate * $row->total_size / 100 / _GB) / 1000;
                            $totalMoney += $num;
                        }

                        //Đã thanh toán, lấy trong bảng Payment, trường user_id, money
                        $paidMoney = \App\Models\Payment::where('user_id', getCurrentUserId())->sum('money');

                        ?>


                                        <h3>Thống kê lượt tải theo tháng</h3>

                        <ul>
                            <li> Tiền thưởng tính bằng 100GB tải xuống * {{$uploaderRate}} nghìn đồng </li>
                            <li> Tích luỹ khi được từ 500k sẽ trả thưởng qua chuyển khoản</li>
                            <li> Không cần bán điểm gold, không cần đua top</li>
                            <li> Lưu ý: Mọi hình thức gian lận sẽ khoá tài khoản trả thưởng</li>
                        </ul>

                        <?php

                        echo "<div class='mb-1'> Tổng tiền thưởng: <b> " . number_formatvn0($totalMoney) . " VND </b> - " . \LadLib\Common\cstring2::toTienVietNamString3($totalMoney) . "</div>";
                        echo "<div class='mb-3'> Đã thanh toán: <b> " . number_formatvn0($paidMoney) . " VND </b> - " . \LadLib\Common\cstring2::toTienVietNamString3($paidMoney) . "
                        <a href='/member/payment'>
                        Xem chi tiết
                        </a>

                        </div>

                        ";

                        ?>

                        <div class="total_sum mb-2"> </div>


                        <table class="table table-bordered table-striped dataTable dtr-inline">
                            <thead>
                            <tr>
                                <th></th>
                                <th>Tháng</th>
                                <th>Số lượt tải</th>
                                <th>Tổng dung lượng</th>
                                <th>Tiền thưởng

                                    (100GB*{{$uploaderRate}}K)
                                </th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($downloadsByMonth as $row)

                            <?php
                            $num = 1000 * 1000 * round( $uploaderRate * $row->total_size / 100 / _GB) / 1000;

                            ?>

                                <tr>
                                    <td>
                                        <input class="check_to_sum" type="checkbox" value="{{ $num }}">
                                    </td>
                                    <td>{{ $row->month }}</td>
                                    <td>{{ number_format( $row->downloads) }}</td>
                                    <td>{{ formatBytes( $row->total_size) }}</td>
                                    <td>
                                        <?php



      echo number_formatvn0( $num ) . "
      <br>" . \LadLib\Common\cstring2::toTienVietNamString3( $num)

           ?>


        </td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>

                        @php
                            // Helper function để format bytes
                            function formatBytes($bytes) {
                                if ($bytes === 0) return '0 Bytes';
                                $k = 1024;
                                $sizes = ['Bytes', 'KB', 'MB', 'GB', 'TB'];
                                $i = floor(log($bytes) / log($k));
                                return number_format($bytes / pow($k, $i), 2) . ' ' . $sizes[$i];
                            }
                        @endphp

                    </div>

            </div>

        </div>


        <div class="content ">
            <div class="container-fluid pt-1">
                <div class="sec1">

                    <b data-code-pos='ppp173506161'>Biểu đồ lượt tải theo tháng (6 tháng)</b>

                    <canvas class="stat_data" id="monthChart" style="height: 240px"></canvas>

                    <b data-code-pos='ppp173506161'>Biểu đồ lượt tải theo Ngày (90 ngày)</b>

                    <canvas class="stat_data" id="dailyChart"
                            style="height: 240px; margin-bottom: 30px"></canvas>

                    <b data-code-pos='ppp1735026161'>Upload 90 ngày</b>
                    <canvas class="stat_data" id="dailyUploadChart"
                            style="height: 240px; margin-bottom: 30px"></canvas>


                </div>
            </div>
        </div>

    </div>

    <!-- /.content -->

@endsection
