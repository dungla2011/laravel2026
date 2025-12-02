@extends("layouts.adm")
@section("title")
    REPORT EVENT
@endsection

<?php
setlocale(LC_ALL, 'vi_VN.utf8');

$uid = getCurrentUserId();

$eid = request('id');
$objData = $ev = \App\Models\EventInfo::find($eid);
// JOIN để chỉ lấy những record có user_event_id tồn tại trong event_user_infos (loại bỏ soft delete)
$mU = \App\Models\EventAndUser::where('event_id', $eid)
    ->join('event_user_infos', 'event_and_users.user_event_id', '=', 'event_user_infos.id')
    ->whereNull('event_user_infos.deleted_at')  // Loại bỏ soft delete
    ->select('event_and_users.*')
    ->get();

?>



<?php



if(request('sort') == 'first_name_asc'){
    $mU = $mU->sortBy(function($item){
        $eu = \App\Models\EventUserInfo::find($item->user_event_id);
        return $eu->first_name;
    }, SORT_LOCALE_STRING);
}
elseif(request('sort') == 'first_name_desc') {
    $mU = $mU->sortByDesc(function ($item) {
        $eu = \App\Models\EventUserInfo::find($item->user_event_id);
        return $eu->first_name;
    }, SORT_LOCALE_STRING);
}

if(request('sort') == 'attend_at_asc'){
    $mU = $mU->sortBy(function($item){
        return $item->attend_at;
    });
}
elseif(request('sort') == 'attend_at_desc') {
    $mU = $mU->sortByDesc(function ($item) {
        return $item->attend_at;
    });
}

if(request('sort') == 'confirm_join_at_asc'){
    $mU = $mU->sortBy(function($item){
        return $item->confirm_join_at;
    });
}
elseif(request('sort') == 'confirm_join_at_desc') {
    $mU = $mU->sortByDesc(function ($item) {
        return $item->confirm_join_at;
    });
}

if(request('user_status') == 'confirm_join'){
    $mU = $mU->filter(function($item){
        return $item->confirm_join_at;
    });
}
elseif(request('user_status') == 'not_confirm_join') {
    $mU = $mU->filter(function($item){
        return !$item->confirm_join_at;
    });
}
elseif(request('user_status') == 'check_in') {
    $mU = $mU->filter(function($item){
        return $item->attend_at;
    });
}
elseif(request('user_status') == 'not_check_in') {
    $mU = $mU->filter(function($item){
        return !$item->attend_at;
    });
}


$mGroup = [];
//Tìm all Group của các user
foreach ($mU AS &$one){
    $user = \App\Models\EventUserInfo::find($one->user_event_id);
    if(!$user)
        continue;

    if($one instanceof \App\Models\EventUserInfo);

    $one->parent_text = "";

    if($user->parent_id)
        if(!in_array($user->parent_id, $mGroup))
            if($x1 = \App\Models\EventUserGroup::find($user->parent_id)){
                $mGroup[$user->parent_id] =  $x1->name;
                $one->parent_text .= $x1->name."; ";
            }
    if($user->parent_extra){
        $m1 = explode(",",$user->parent_extra);
        if($m1)
            foreach ($m1 AS $g1){
                if(!in_array($g1, $mGroup))
                    if($x1 = \App\Models\EventUserGroup::find($g1)) {
                        $one->parent_text .= $x1->name."; ";
                        $mGroup[$g1] = $x1->name;
                    }
            }
    }
}

//dump($mU);
//die();

//Chi lay ra cac user thuoc group
if($gid = request('group_id')){
    $mU = $mU->filter(function($item) use ($gid){
        $eu = \App\Models\EventUserInfo::find($item->user_event_id);

        if(!$eu)
            return false;

        if($eu->parent_id == $gid)
            return true;
        if($eu->parent_extra){
            $m1 = explode(",",$eu->parent_extra);
            if(in_array($gid, $m1))
                return true;
        }
        return false;
    });
}

?>
@section('title_nav_bar')
{{--    {{$ev->name ?? ''}}--}}
@endsection
<?php
//                            echo "<div style='text-align: center'> $ev->name </div>";
// Chỉ đếm những record có user_event_id tồn tại trong event_user_infos (loại bỏ soft delete)
$nUser = \App\Models\EventAndUser::where('event_id', $eid)
    ->join('event_user_infos', 'event_and_users.user_event_id', '=', 'event_user_infos.id')
    ->whereNull('event_user_infos.deleted_at')
    ->count();
$nCoMat = \App\Models\EventAndUser::where('event_id', $eid)
    ->join('event_user_infos', 'event_and_users.user_event_id', '=', 'event_user_infos.id')
    ->whereNull('event_user_infos.deleted_at')
    ->whereNotNull('event_and_users.attend_at')
    ->count();

if(isAdminCookie()){
//    dump(" NU  =$nUser");
}

?>

@section('js')
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            // Gọi hàm getUserDataList với tham số 113
            setTimeout(function(){
                // location.reload();
                getUserDataList(<?php echo $eid ?>);




            }, 100);

            $("#export_excel1").on("click", function() {
                // Gọi hàm getUserDataList với tham số 113

                console.log(" dataToExportExcel = " , dataToExportExcel);
                window.exportJsonToExcel(dataToExportExcel, 'event_users_export.csv');

            });

        });


        // FUNCTION: Export JSON data to Excel
        window.exportJsonToExcel = function(jsonData, filename = null) {
            console.log("🔍 Starting JSON to Excel export...");

            if (!jsonData || !Array.isArray(jsonData) || jsonData.length === 0) {
                alert("Dữ liệu JSON không hợp lệ hoặc rỗng");
                return false;
            }

            console.log(`📊 Exporting ${jsonData.length} records from JSON`);

            // ===== BƯỚC 1: TẠO HEADERS TỪ JSON KEYS =====
            let headers = [
                'STT',
                'User ID',
                'Họ tên',
                'Danh xưng',
                'Email',
                'Điện thoại',
                'Ngôn ngữ',
                'Tổ chức',
                'Nhóm/Phòng ban',
                'Xác nhận tham gia',
                'Từ chối tham dự',
                'Có mặt lúc',
                'Ghi chú (EAU)',
                'Ghi chú (User)'
            ];

            console.log("📋 Headers:", headers);

            // ===== BƯỚC 2: XỬ LÝ DỮ LIỆU ROWS =====
            let exportData = [];

            jsonData.forEach((item, index) => {
                // Format dates
                let confirmDate = item.confirm_join_at ? formatDateTime(item.confirm_join_at) : '';
                let denyDate = item.deny_join_at ? formatDateTime(item.deny_join_at) : '';
                let attendDate = item.attend_at ? formatDateTime(item.attend_at) : '';

                // Create full name with title
                let fullName = '';
                if (item.title && item.name) {
                    fullName = `${item.title} ${item.name}`;
                } else if (item.name) {
                    fullName = item.name;
                }

                let rowData = [
                    index + 1,                          // STT
                    item.user_id || '',                 // User ID
                    fullName,                           // Họ tên
                    item.title || '',                   // Danh xưng
                    item.email || '',                   // Email
                    item.phone || '',                   // Điện thoại
                    item.language || 'vi',              // Ngôn ngữ
                    item.organization || '',            // Tổ chức
                    item.parent_name || '',             // Nhóm/Phòng ban
                    confirmDate,                        // Xác nhận tham gia
                    denyDate,                           // Từ chối tham dự
                    attendDate,                         // Có mặt lúc
                    item.note_eau || '',                // Ghi chú (EAU)
                    item.note_u || ''                   // Ghi chú (User)
                ];

                exportData.push(rowData);
            });

            console.log(`✅ Processed ${exportData.length} rows`);

            // ===== BƯỚC 3: TẠO EXCEL FILE =====
            try {
                // Kiểm tra XLSX library
                if (typeof XLSX === 'undefined') {
                    console.warn("XLSX library not found, using fallback CSV export");
                    exportJsonToCSV([headers, ...exportData], filename);
                    return false;
                }

                // Tạo Excel file
                let ws = XLSX.utils.aoa_to_sheet([headers, ...exportData]);
                let wb = XLSX.utils.book_new();
                XLSX.utils.book_append_sheet(wb, ws, "User Data");

                // Auto-fit columns
                let wscols = [];
                headers.forEach(() => {
                    wscols.push({wch: 15}); // Set width for each column
                });
                ws['!cols'] = wscols;

                // Tạo tên file
                let finalFilename = filename || generateTimestampFilename('user_data_export', 'xlsx');

                // Download file
                XLSX.writeFile(wb, finalFilename);

                alert(`✅ Đã export ${exportData.length} dòng dữ liệu thành công!\nFile: ${finalFilename}\nCột: ${headers.length}`);

                return true;

            } catch (error) {
                console.error("Error creating Excel file:", error);
                console.warn("Fallback to CSV export");
                exportJsonToCSV([headers, ...exportData], filename);
                return false;
            }
        };

        // HELPER FUNCTION: Format DateTime
        function formatDateTime(dateString) {
            if (!dateString) return '';
            try {
                let date = new Date(dateString);
                return date.toLocaleString('vi-VN', {
                    day: '2-digit',
                    month: '2-digit',
                    year: 'numeric',
                    hour: '2-digit',
                    minute: '2-digit',
                    second: '2-digit'
                });
            } catch (error) {
                return dateString; // Return original if can't parse
            }
        }

        // HELPER FUNCTION: Generate timestamp filename
        function generateTimestampFilename(prefix, extension) {
            let now = new Date();
            let timestamp = now.getFullYear() +
                (now.getMonth() + 1).toString().padStart(2, '0') +
                now.getDate().toString().padStart(2, '0') + '_' +
                now.getHours().toString().padStart(2, '0') +
                now.getMinutes().toString().padStart(2, '0');
            return `${prefix}_${timestamp}.${extension}`;
        }

        // FALLBACK FUNCTION: Export to CSV
        function exportJsonToCSV(data, filename = null) {
            let csvContent = data.map(row =>
                row.map(cell => `"${(cell || '').toString().replace(/"/g, '""')}"`).join(',')
            ).join('\n');

            let blob = new Blob(['\uFEFF' + csvContent], { type: 'text/csv;charset=utf-8;' });
            let link = document.createElement('a');

            if (link.download !== undefined) {
                let finalFilename = filename || generateTimestampFilename('user_data_export', 'csv');

                let url = URL.createObjectURL(blob);
                link.setAttribute('href', url);
                link.setAttribute('download', finalFilename);
                link.style.visibility = 'hidden';
                document.body.appendChild(link);
                link.click();
                document.body.removeChild(link);

                showToastInfoBottom(`✅ Đã export ${data.length - 1} dòng dữ liệu thành công!\nFile: ${finalFilename}`);
            }
        }

        // DEMO FUNCTION: Export sample data
        window.exportSampleData = function() {
            let sampleData = [
                {
                    "user_id": 174,
                    "email": "anhdaomofa@dav.edu.vn",
                    "phone": null,
                    "language": "vi",
                    "title": "Bà",
                    "name": "Đào Thị Mai Anh",
                    "note_eau": null,
                    "confirm_join_at": null,
                    "deny_join_at": null,
                    "attend_at": null,
                    "note_u": null,
                    "organization": null,
                    "parent_name": "Viện NCCL"
                },
                {
                    "user_id": 177,
                    "email": "pmtranganh-VCL@dav.edu.vn",
                    "phone": null,
                    "language": "vi",
                    "title": "Bà",
                    "name": "Phạm Mai Trang Anh",
                    "note_eau": null,
                    "confirm_join_at": "2025-07-02 14:49:57",
                    "deny_join_at": null,
                    "attend_at": "2025-07-03 08:34:22",
                    "note_u": null,
                    "organization": null,
                    "parent_name": "Viện NCCL"
                }
            ];

            exportJsonToExcel(sampleData, 'sample_export.xlsx');
        };

        // MAIN EXPORT FUNCTION for dataToExportExcel
        window.exportDataToExportExcel = function() {
            // Check if dataToExportExcel exists in global scope
            if (typeof dataToExportExcel !== 'undefined' && dataToExportExcel) {
                console.log("Found dataToExportExcel variable, exporting...");
                exportJsonToExcel(dataToExportExcel, 'event_users_export.xlsx');
            } else {
                console.log("dataToExportExcel not found in global scope");
                alert("Biến dataToExportExcel chưa được định nghĩa. Hãy đảm bảo dữ liệu JSON đã được load.");
            }
        };


        $(function (){


            $("body").addClass("sidebar-collapse")

            $('select.set_color').each(function() {
                // Kiểm tra giá trị của select
                if (this.value !== '') {
                    $(this).css('color', 'red'); // Thay đổi màu sắc nếu select có value
                    $(this).css('border-color', 'red'); // Thay đổi màu sắc nếu select có value
                    $(this).css('font-weight', 'bold'); // Thay đổi màu sắc nếu select có value
                } else {
                    $(this).css('color', 'black'); // Trở lại màu ban đầu nếu select không có value
                }
            });

            // Lắng nghe sự kiện 'change' trên tất cả các phần tử select
            $('select.set_color').on('change', function() {
                if (this.value !== '') {
                    $(this).css('color', 'red'); // Thay đổi màu sắc nếu select có value
                    $(this).css('border-color', 'red'); // Thay đổi màu sắc nếu select có value
                    $(this).css('font-weight', 'bold'); // Thay đổi màu sắc nếu select có value
                } else {
                }
            });
        })

        //

        function showHideRowWithValue(value){

            console.log(" showHideRowWithValue ", value);

            let numberShow = 0;
            // Duyệt qua tất cả các phần tử 'tr' trong 'tbody'
            $('tbody tr.user-row').each(function() {
                var isMatch = false;

                // Duyệt qua tất cả các phần tử 'td.for_search' trong 'tr'
                $(this).find('td.for_search').each(function() {
                    // Lấy text từ phần tử 'td'
                    var tdText = $(this).text().toLowerCase();

                    // Kiểm tra xem text của 'td' có chứa giá trị từ input không
                    if (tdText.indexOf(value) > -1) {
                        isMatch = true;
                        $(this).addClass('highlight'); // Thêm class 'highlight' vào 'td' chứa text
                        return false; // thoát khỏi vòng lặp .each() ngay lập tức khi tìm thấy kết quả phù hợp
                    } else {
                        // $(this).removeClass('highlight'); // Xóa class 'highlight' nếu 'td' không chứa text
                    }
                });

                if (isMatch) {
                    // Nếu có, hiển thị phần tử 'tr'
                    numberShow++;
                    $(this).show();
                } else {
                    // Nếu không, ẩn phần tử 'tr'
                    $(this).hide();
                }


                // filterUserRows();

            });

            console.log(" numberShow = ", numberShow);
        }

        $(document).ready(function() {
            // Lắng nghe sự kiện 'keyup' trên input có class 'for_search'
            $('input.for_search').on('keyup', function() {
                var value = $(this).val().toLowerCase();
                console.log(" Enter key : " , value);

                // Gọi hàm tổng hợp để xử lý tất cả filter cùng lúc
                filterUserRows();
            });
        });

        // $(document).ready(function() {
        //     // Lắng nghe sự kiện 'keyup' trên input có class 'for_search'
        //     $('input.for_search').on('keyup', function() {
        //         // Lấy giá trị từ input
        //
        //         var value = $(this).val().toLowerCase();
        //         // Xóa class 'highlight' từ tất cả các phần tử 'td'
        //
        //         console.log(" Enter key : " , value);
        //
        //         $('td.for_search').removeClass('highlight');
        //
        //         if(value.length == 0){
        //             $('tbody tr').each(function() {
        //                 $(this).show();
        //             });
        //             return;
        //         }
        //
        //
        //         showHideRowWithValue(value)
        //
        //     });
        // });

    </script>
@endsection

@section('header')
    @include('parts.header-all')
@endsection

@section('css')
    <link rel="stylesheet"
          href="/vendor/div_table2/div_table2.css?v=<?php echo filemtime(public_path().'/vendor/div_table2/div_table2.css'); ?>">
    <link rel="stylesheet" href="/admins/upload_file.css?v=<?php echo filemtime(public_path().'/admins/upload_file.css'); ?>">

    <style>
        .highlight {
            color: red;
        }
        .table-striped td {
            padding: 3px 6px;
            font-size: 85%;
        }
        label {
            font-size: 90%;
        }
        b.blue {
            color: royalblue;
        }
        .info-box-text {
            /*font-style: italic;*/
            display: inline-block;
            padding-top: 2px;
        }
        table.table-striped {
            border-spacing: 1px;
        }
        .table thead th {
            border-bottom: 2px solid #bbb!important;
        }
        .table-striped th {
            font-size: 85%;
            padding: 3px 5px;
        }
        select.form-control{
            padding: 1px 10px;
            font-size: 90%;
        }

        /*body:not(.sidebar-mini-md):not(.sidebar-mini-xs):not(.layout-top-nav) .content-wrapper, body:not(.sidebar-mini-md):not(.sidebar-mini-xs):not(.layout-top-nav) .main-footer, body:not(.sidebar-mini-md):not(.sidebar-mini-xs):not(.layout-top-nav) .main-header*/
        /*{*/
        /*    margin-left: 0px !important;*/
        /*}*/
        /*aside {*/
        /*    display: none;*/
        /*}*/
        /*.content-header {*/
        /*     padding: 0px;*/
        /*}*/
        /*nav.navbar{*/
        /*    display: none!important;*/
        /*}*/
        /*::-webkit-scrollbar {*/
        /*    height: 4px;              !* height of horizontal scrollbar ← You're missing this *!*/
        /*    width: 4px;               !* width of vertical scrollbar *!*/
        /*    border: 1px solid gray;*/
        /*}*/
    </style>
@endsection

@section("content")
    <?php
        if(\App\Components\Helper1::isMemberModule())
            $mm = \App\Models\EventInfo::where("user_id", $uid)->orderBy("id", 'desc')->get();
        else
            $mm = \App\Models\EventInfo::orderBy("id", 'desc')->get();
    ?>
    <div class="content-wrapper">
        <!-- Main content -->
        <section class="content" data-code-pos='ppp17222453708171'>
            <div class="container-fluid">

                <div class="row pt-3">
                    <div class="col-sm-12">
                        <div class="form-group">
                            <select onchange="location.href=this.value" class="custom-select" style="font-weight: bold; color: dodgerblue; border-color: #ccc">
                                <option value=""> :: Select Event :: </option>
                                <?php
                                foreach ($mm as $m){
                                    $linkOpt = \LadLib\Common\UrlHelper1::setUrlParamThisUrl('id', $m->id);
                                    if($m->id == $eid)
                                        echo "<option value='$linkOpt' selected> $m->id . $m->name</option>";
                                    else
                                        echo "<option value='$linkOpt'>  $m->id . $m->name</option>";
                                }
                                ?>
                            </select>
                        </div>
                    </div>
                </div>

                <!--
                <div class="row">

                    <div class="col-md-4">
                        <div class="info-box">
                            <span class="info-box-icon bg-primary"><i class="fa fa-users"></i></span>
                            <div class="info-box-content">
                                <span class="info-box-text">User</span>
                                <span class="info-box-number">{{$nUser}}</span>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-4">
                        <div class="info-box">
                            <span class="info-box-icon bg-success"><i class="far fa-flag"></i></span>
                            <div class="info-box-content">
                                <span class="info-box-text">Checked-in</span>
                                <span class="info-box-number">{{$nCoMat}}</span>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-4">
                        <div class="info-box">
                            <span class="info-box-icon bg-warning"><i class="far fa-question-circle"></i></span>
                            <div class="info-box-content">
                                <span class="info-box-text">Haven’t checked-in</span>
                                <span class="info-box-number">{{$nUser - $nCoMat}}</span>
                            </div>
                        </div>
                    </div>

                </div>

-->

                <div class="row" style="">
                    <div class="col-sm-6">
                        <div class="mb-2">
                            <div class="input-group">
                                <input style="color: red" type="search" class="for_search form-control form-control-sm"
                                       placeholder="Nhập từ tìm kiếm">
                                <div class="input-group-append">
                                    <button type="submit" class="btn btn-sm btn-default">
                                        <i class="fa fa-search"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-6" style="font-size: 90%">
                        <div class="row">
                            <div class="col-md-4">
                                <span class="info-box-text" data-code-pos='ppp17526743013521'>User: <b class="blue" id="n_user_select"> {{$nUser}} </b></span>
                            </div>
                            <div class="col-md-4">
                                <span class="info-box-text">Checked-in: <b class="blue">{{$nCoMat}} </b></span>
                            </div>
                            <div class="col-md-4">
                                <span class="info-box-text">
                                    Haven’t checked-in: <b class="blue"> {{$nUser - $nCoMat}} </b></span>

                                <div style="float: right" id='export_excel1' title="export excel">
                                    <i title="Export to Excel" style="float: right; color: green;  font-size: 1.4em" class="fa fa-file-excel ml-3"></i>

                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div id="table_list_user_all" style="">  </div>

                <?php
                $linkOpt = \LadLib\Common\UrlHelper1::setUrlParamThisUrl('group_id', null);
                ?>




                <div class="row" style="display: none">
                    <div class="col-sm-2">
                        <div class="form-group">
                            <label>Chọn nhóm</label>
                            <select onchange="location.href=this.value || '<?php echo $linkOpt ?>'" class="form-control form-control-sm set_color">
                                <?php
                                echo "<option value='' selected>:: Chọn nhóm ::</option>";
                                foreach ($mGroup AS $gid=> $gname) {
                                    $linkOpt = \LadLib\Common\UrlHelper1::setUrlParamThisUrl('group_id', $gid);

                                    if($gid == request('group_id'))
                                        echo "<option value='$linkOpt' selected>$gname</option>";
                                    else
                                        echo "<option value='$linkOpt'>$gname</option>";
                                }
                                ?>

                            </select>
                        </div>
                    </div>
                    <?php
                    $linkOpt0 = \LadLib\Common\UrlHelper1::getUriWithoutParam() . '?id='.$eid;
                    ?>

                    <div class="col-sm-2" >
                        <div class="form-group">
                            <label>Trạng thái tham dự.</label>
                            <select onchange="location.href=this.value || '<?php echo $linkOpt0 ?>'" class="form-control form-control-sm set_color">
                            <option value="">:: Chọn ::</option>
                                <?php

                                    $mmStt = [
                                        'confirm_join' => 'Đã xác nhận tham gia',
                                        'not_confirm_join' => 'Chưa xác nhận tham gia',
                                        'check_in' => 'Đã check-in',
                                        'not_check_in' => 'Chưa check-in',
                                    ];
                                    foreach ($mmStt AS $k=>$v){
                                        $linkOpt = \LadLib\Common\UrlHelper1::setUrlParam($linkOpt0, 'user_status', $k);
                                        if($k == request('user_status'))
                                            echo "<option value='$linkOpt' selected>$v</option>";
                                        else
                                            echo "<option value='$linkOpt'>$v</option>";
                                    }

                                ?>
                            </select>
                        </div>
                    </div>

                    <div class="col-sm-2">
                        <div class="form-group">
                            <label>Select 3</label>
                            <select class="form-control form-control-sm set_color" >
                                <option value="">:: Chọn ::</option>
                                <option>Chọn 3</option>
                                <option>option 2</option>
                                <option>option 3</option>
                                <option>option 4</option>
                                <option>option 5</option>
                            </select>
                        </div>
                    </div>

                    <div class="col-sm-2">
                        <div class="form-group">
                            <label>Select 4</label>
                            <select class="form-control form-control-sm set_color" >
                                <option value="">:: Chọn ::</option>
                                <option>option 2</option>
                                <option>option 3</option>
                                <option>option 4</option>
                                <option>option 5</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-sm-2">
                        <div class="form-group">
                            <label>Select 5</label>
                            <select class="form-control form-control-sm set_color" >
                                <option value="">:: Chọn ::</option>
                                <option>option 2</option>
                                <option>option 3</option>
                                <option>option 4</option>
                                <option>option 5</option>
                            </select>
                        </div>
                    </div>

                    <div class="col-sm-2">
                        <div class="form-group">
                            <label>Select 6</label>
                            <select class="form-control form-control-sm set_color" >
                                <option value="">Chọn 3</option>
                                <option>option 2</option>
                                <option>option 3</option>
                                <option>option 4</option>
                                <option>option 5</option>
                            </select>
                        </div>
                    </div>
                </div>

                <div class="row" data-code-pos='ppp17222453531761' style="display: none">
                    <div class="col-sm-12">
                        <table id="example2" class="table table-bordered table-striped dataTable dtr-inline"
                               aria-describedby="example2_info">
                            <thead>
                            <tr>
                                <th class="sorting sorting_asc text-center" tabindex="0" aria-controls="example2" rowspan="1"
                                    colspan="1" aria-sort="ascending">
                                    No
                                </th>
                                <th class="sorting sorting_asc" tabindex="0" aria-controls="example2" rowspan="1"
                                    colspan="1" aria-sort="ascending">
                                    Title
                                </th>

                                <th class="sorting sorting_asc" tabindex="0" aria-controls="example2" rowspan="1"
                                    colspan="1" aria-sort="ascending">
                                    Lastname
                                </th>


                                <th class="sorting sorting_asc" tabindex="0" aria-controls="example2" rowspan="1"
                                    colspan="1" aria-sort="ascending">
                                    <?php
                                        if(!request('sort') || request('sort') == 'first_name_asc')
                                            $linkSort = \LadLib\Common\UrlHelper1::setUrlParamThisUrl('sort', 'first_name_desc');
                                        else
                                            $linkSort = \LadLib\Common\UrlHelper1::setUrlParamThisUrl('sort', 'first_name_asc');
                                    ?>
                                    <a href="{{$linkSort}}">
                                    <i class="fas fa-sort" title="Bấm vào để sắp xếp Tăng giảm"></i>
                                        Firstname
                                    </a>



                                </th>

                                <th class="sorting sorting_asc" tabindex="0" aria-controls="example2" rowspan="1"
                                    colspan="1" aria-sort="ascending">
                                    Group

                                </th>

                                <th class="sorting sorting_asc" tabindex="0" aria-controls="example2" rowspan="1"
                                    colspan="1" aria-sort="ascending">
                                    Organization

                                </th>

                                <th class="sorting sorting_asc" tabindex="0" aria-controls="example2" rowspan="1"
                                    colspan="1" aria-sort="ascending">
                                    Info
                                </th>
                                <th class="sorting sorting_asc text-center" tabindex="0" aria-controls="example2" rowspan="1"
                                    colspan="1" aria-sort="ascending">
                                    <?php
                                    if(!request('sort') || request('sort') == 'confirm_join_at_asc')
                                        $linkSort1 = \LadLib\Common\UrlHelper1::setUrlParamThisUrl('sort', 'confirm_join_at_desc');
                                    else
                                        $linkSort1 = \LadLib\Common\UrlHelper1::setUrlParamThisUrl('sort', 'confirm_join_at_asc');
                                    ?>

                                    <a href="{{$linkSort1}}">
                                        <i class="fas fa-sort" title="Bấm vào để sắp xếp Tăng giảm"></i>
                                        Confirmed
                                    </a>

                                </th>
                                <th class="sorting sorting_asc text-center" tabindex="0" aria-controls="example2" rowspan="1"
                                    colspan="1" aria-sort="ascending">
                                    <?php
                                    if(!request('sort') || request('sort') == 'attend_at_asc')
                                        $linkSort = \LadLib\Common\UrlHelper1::setUrlParamThisUrl('sort', 'attend_at_desc');
                                    else
                                        $linkSort = \LadLib\Common\UrlHelper1::setUrlParamThisUrl('sort', 'attend_at_asc');
                                    ?>
                                    <a href="{{$linkSort}}">
                                        <i class="fas fa-sort" title="Bấm vào để sắp xếp Tăng giảm"></i>
                                        Check-in
                                    </a>


                                </th>


                                <th class="sorting sorting_asc" tabindex="0" aria-controls="example2" rowspan="1"
                                    colspan="1" aria-sort="ascending">
                                    Note
                                </th>
                            </tr>
                            </thead>
                            <tbody>
                            <?php



                            $cc = 0;
                            foreach($mU AS $item) {
                                $ev = \App\Models\EventInfo::find($item->event_id);
                                if(!$ev )
                                    continue;
                                $eu = \App\Models\EventUserInfo::find($item->user_event_id);

                                if(!$eu)
                                    continue;

                                if($item instanceof \App\Models\EventUserInfo);

                                if(0);
                                    //Load tu js nen ko can o day nua
//                                if($ev){
//                                    $cc++;
//                                    echo "<tr data-code-pos='ppp17222457116281' class='one_row user-row'>";
//                                    echo "<td class='text-center'>$cc</td>";
//                                    echo "<td>$eu->title</td>";
//                                    echo "<td class='for_search last_name '>$eu->last_name</td>";
//                                    echo "<td class='for_search first_name'>$eu->first_name</td>";
//                                    echo "<td class='for_search parent_text' data-x='12312312334'> $item->parent_text </td>";
//                                    echo "<td class='for_search organization'> $eu->organization </td>";
//
//                                    if($item->attend_at)
//                                        echo "<td>
//                                            <i>
//                                            Có mặt
//                                            </i>
//                                            </td>";
//                                    else
//                                        echo "<td></td>";
//
//                                    echo "<td class='text-center'> $item->confirm_join_at</td>";
//                                    echo "<td class='text-center'>$item->attend_at</td>";
//
//                                    echo "<td></td>";
//                                    echo "</tr>";
//                                }
                            }
                            ?>
                            <tr class="odd">
                            </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

            </div>
        </section>
        <!-- /.content -->
    </div>



    <?php

//    if(isDebugIp())
    {

    require "/var/www/html/app/Models/EventInfo_Meta_js_edit.php";






    }


    ?>

@endsection
