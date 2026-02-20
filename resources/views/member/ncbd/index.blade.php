<?php

use LadLib\Common\Database\MetaClassCommon;
use App\Models\EventUserInfo;

$eventUserInfo = EventUserInfo::where('email', getCurrentUserEmail())->first();

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
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/choices.js/10.2.0/choices.min.css" />

    <?php

        \App\Models\BlockUi::showCssHoverBlock();

?>
@endsection

@section('js')
    <script src="/admins/table_mng.js"></script>
    <script src="/vendor/div_table2/div_table2.js"></script>
    <script src="/admins/meta-data-table/meta-data-table.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/choices.js/10.2.0/choices.min.js"></script>

    <script>
        $("#btn-show-token").on('click', function () {
            $("#user_token").toggle();
        })

        // Initialize Choices.js for bank select
        $(document).ready(function() {
            const bankSelectElement = document.getElementById('bankSelect');
            if (bankSelectElement) {
                const choices = new Choices('#bankSelect', {
                    searchEnabled: true,
                    searchChoices: true,
                    searchFloor: 1,
                    searchResultLimit: 10,
                    position: 'bottom',
                    allowHTML: false,
                    placeholder: true,
                    placeholderValue: 'Chọn hoặc gõ tên ngân hàng...',
                    searchPlaceholderValue: 'Gõ để tìm kiếm...',
                    noResultsText: 'Không tìm thấy ngân hàng nào',
                    noChoicesText: 'Không có lựa chọn nào',
                    itemSelectText: 'Nhấn để chọn',
                    removeItemButton: false,
                    shouldSort: false,
                    duplicateItemsAllowed: false,
                });

                // Handle selection change
                bankSelectElement.addEventListener('change', function(event) {
                    const selectedValue = event.target.value;
                    document.getElementById('bank_name_text').value = selectedValue;
                    console.log('Selected bank:', selectedValue);
                });
            }
        });

        // Handle form submission for updating user info
        $("#btnUpdateInfo").on('click', function(e) {
            e.preventDefault();

            const $form = $("#updateUserInfoForm");
            const $btn = $(this);
            const $msg = $("#updateMessage");
            const originalBtnText = $btn.html();

            $btn.prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i> Đang cập nhật...');
            $msg.html('');

            const formData = $form.serializeArray();
            const data = {};
            formData.forEach(item => {
                // Skip search_terms field added by Choices.js
                if (item.name !== 'search_terms') {
                    data[item.name] = item.value;
                }
            });

            // Get user token
            const token = "<?php echo Auth()->user()->getJWTUserToken(); ?>";

            // Get existing record ID or create new
            <?php if ($eventUserInfo): ?>
            const recordId = <?= $eventUserInfo->id ?>;
            const apiUrl = `/api/member-event-user-info/update/${recordId}`;
            const method = 'POST';
            <?php else: ?>
            const recordId = null;
            const apiUrl = '/api/member-event-user-info/add';
            const method = 'POST';
            <?php endif; ?>

            $.ajax({
                url: apiUrl,
                method: method,
                headers: {
                    'Authorization': 'Bearer ' + token,
                    'Content-Type': 'application/json',
                    'Accept': 'application/json'
                },
                data: JSON.stringify(data),
                success: function(response) {
                    showToastInfoTop("Cập nhật thành công!");
                    $msg.html('<div class="alert alert-success"><i class="fa fa-check-circle"></i> Cập nhật thông tin thành công!</div>');
                    // Don't reload page - just show success message
                },
                error: function(xhr) {
                    showToastWarningTop("Có lỗi cập nhật!");
                    let errorMsg = 'Có lỗi xảy ra, vui lòng thử lại!';
                    if (xhr.responseJSON && xhr.responseJSON.errors) {
                        const errors = xhr.responseJSON.errors;
                        errorMsg = '<ul class="mb-0">';
                        Object.keys(errors).forEach(key => {
                            errors[key].forEach(err => {
                                errorMsg += `<li>${err}</li>`;
                            });
                        });
                        errorMsg += '</ul>';
                    } else if (xhr.responseJSON && xhr.responseJSON.message) {
                        errorMsg = xhr.responseJSON.message;
                    }
                    $msg.html('<div class="alert alert-danger"><i class="fa fa-exclamation-circle"></i> ' + errorMsg + '</div>');
                },
                complete: function() {
                    $btn.prop('disabled', false).html(originalBtnText);
                }
            });
        });
    </script>
@endsection

@section("content")

    <style>
        .member_zone i {
            color: #555555;
        }
        .img-responsive-glx {max-width: 1200px; height: auto; }
    </style>
    <?php

    $user = \Illuminate\Support\Facades\Auth::user();

    ?>

    <div class="content-wrapper member_zone">
        <!-- Content Header (Page header) -->
        <div class="content-header">

        </div>
        <!-- /.content-header -->

        <!-- Main content -->
        <section class="content">
            <div class="container-fluid">

                <div
                    style="border-bottom:  1px solid #ccc; margin: -10px 0px 10px 0px;padding : 20px; background: white" data-code-pos='ppp17698573676521'>


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

                <?php

                $email =  getCurrentUserEmail();

                // Get EventUserInfo for current user
                $eventUserInfo = \App\Models\EventUserInfo::where('email', $email)->first();
                ?>

                <div class="mt-3 form_update_event_user_info"
                    style="border-bottom:  1px solid #ccc; margin: 0px ;max-width: 800px; padding : 20px; background: white" data-code-pos='ppp17698573676521'>

                    <h4 class="mb-3"><i class="fa fa-fw fa-user-circle"></i>
                        Thông tin cá nhân
                    </h4>
                    <form id="updateUserInfoForm" method="POST" onsubmit="return false;">
                        <div class="row">
                            <div class="form-group col-md-2">
                                <label for="title">Danh xưng</label>
                                <select class="form-control" id="title" name="title">
                                    <option value="">---</option>
                                    <option value="Mr" <?= ($eventUserInfo->title ?? '') == 'Mr' ? 'selected' : '' ?>>Mr</option>
                                    <option value="Ms" <?= ($eventUserInfo->title ?? '') == 'Ms' ? 'selected' : '' ?>>Ms</option>
                                    <option value="Mrs" <?= ($eventUserInfo->title ?? '') == 'Mrs' ? 'selected' : '' ?>>Mrs</option>
                                </select>
                            </div>

                            <div class="form-group col-md-5">
                                <label for="first_name">Tên <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="first_name" name="first_name"
                                       value="<?= $eventUserInfo->first_name ?? '' ?>" required>
                            </div>

                            <div class="form-group col-md-5">
                                <label for="last_name">Họ <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="last_name" name="last_name"
                                       value="<?= $eventUserInfo->last_name ?? '' ?>" required>
                            </div>
                        </div>

                        <div class="row">
                            <div class="form-group col-md-6">
                                <label for="email">Email <span class="text-danger">*</span></label>
                                <input type="email" class="form-control" id="email" name="email"
                                       value="<?= $email ?>" readonly style="background-color: #f5f5f5;">
                            </div>

                            <div class="form-group col-md-6">
                                <label for="phone">Số điện thoại</label>
                                <input type="tel" class="form-control" id="phone" name="phone"
                                       value="<?= $eventUserInfo->phone ?? '' ?>" placeholder="0912345678">
                            </div>
                        </div>

                        <div class="row">
                            <div class="form-group col-md-3">
                                <label for="gender">Giới tính</label>
                                <select class="form-control" id="gender" name="gender">
                                    <option value="">---</option>
                                    <option value="1" <?= ($eventUserInfo->gender ?? '') == '1' ? 'selected' : '' ?>>Nam</option>
                                    <option value="2" <?= ($eventUserInfo->gender ?? '') == '2' ? 'selected' : '' ?>>Nữ</option>
                                </select>
                            </div>

                            <div class="form-group col-md-4">
                                <label for="id_number">CMND/CCCD</label>
                                <input type="text" class="form-control" id="id_number" name="id_number"
                                       value="<?= $eventUserInfo->id_number ?? '' ?>">
                            </div>

                            <div class="form-group col-md-5">
                                <label for="tax_number">Mã số thuế</label>
                                <input type="text" class="form-control" id="tax_number" name="tax_number"
                                       value="<?= $eventUserInfo->tax_number ?? '' ?>">
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="organization">Tổ chức/Đơn vị</label>
                            <input type="text" class="form-control" id="organization" name="organization"
                                   value="<?= $eventUserInfo->organization ?? '' ?>">
                        </div>

                        <div class="form-group">
                            <label for="designation">Chức danh</label>
                            <input type="text" class="form-control" id="designation" name="designation"
                                   value="<?= $eventUserInfo->designation ?? '' ?>">
                        </div>

                        <div class="form-group">
                            <label for="address">Địa chỉ</label>
                            <textarea class="form-control" id="address" name="address" rows="2"><?= $eventUserInfo->address ?? '' ?></textarea>
                        </div>

                        <div class="row">
                            <div class="form-group col-md-4">
                                <label for="bank_name_text">Ngân hàng</label>
                                <select class="form-control" id="bankSelect">
                                    <option value="">-- Chọn ngân hàng --</option>
                                    @php
                                        $banks = config('banks');
                                        asort($banks);
                                        $currentBank = $eventUserInfo->bank_name_text ?? '';
                                        foreach($banks as $code => $name){
                                            $publicName = $name['public_name'] ?? '';
                                            $selected = ($code == $currentBank) ? 'selected' : '';
                                            echo "<option value='$code' $selected>$publicName</option>";
                                        }
                                    @endphp
                                </select>
                                <input type="hidden" id="bank_name_text" name="bank_name_text" value="<?= $eventUserInfo->bank_name_text ?? '' ?>">
                            </div>

                            <div class="form-group col-md-8">
                                <label for="bank_acc_number">Số tài khoản ngân hàng</label>
                                <input type="text" class="form-control" id="bank_acc_number" name="bank_acc_number"
                                       value="<?= $eventUserInfo->bank_acc_number ?? '' ?>">
                            </div>
                        </div>

{{--                        <div class="form-group">--}}
{{--                            <label for="note">Ghi chú</label>--}}
{{--                            <textarea class="form-control" id="note" name="note" rows="3"><?= $eventUserInfo->note ?? '' ?></textarea>--}}
{{--                        </div>--}}

                        <div class="form-group text-center mt-4">
                            <button type="button" class="btn btn-primary" id="btnUpdateInfo">
                                <i class="fa fa-save"></i> Cập nhật thông tin
                            </button>
                            <div id="updateMessage" class="mt-3"></div>
                        </div>
                    </form>

                </div>


                <?php

                //Kiểm tra user có RoleID 1 hay 2 không
                if ($user instanceof \App\Models\User) ;

                if ($user->hasRole(1) || $user->hasRole(2)){
//                        echo "<div class='alert alert-warning'> <i class='fa fa-fw fa-exclamation-triangle'></i> Bạn là Admin hoặc Super Admin</div>";

                    ?>


                <div style="border-bottom:  1px solid #ccc; margin: 20px 0px;padding : 20px; background: white">
                    <div class="row" style="">
                        <div class="col-sm-12 qqqq1111">
                            <?php
//                                $ui = new \App\Models\BlockUi();
//                                $ui->showEditButton('member_edit');

                            $ui = \App\Models\BlockUi::showEditButtonStatic('member_edit');
                            echo $ui->content;

                            ?>
                        </div>
                    </div>

                    <div class="row" style="display: none">
                        <div class="col-sm-12">

                            <i class="fa fa-fw fa-home"></i>
                            Đơn vị trực thuộc:
                            <span style="font-weight: bold">

                                        {!! $depName !!}
                                    </span>
                        </div>

                        <div class="col-sm-12 mt-3 mb-2">
                            <i class="fa fa-fw fa-users"></i>
                            Các thành viên cùng đơn vị:
                        </div>


                        {!! $userList !!}
                    </div>
                    {{--                            <dd class="col-sm-8 offset-sm-4">Donec id elit non mi porta gravida at eget metus.</dd>--}}

                </div>

                    <?php
                }
                ?>


            </div>
        </section>
        <!-- /.content -->


        <!-- Modal -->
        <div class="modal fade" id="exampleModalCenter" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalCenterTitle">Modal title</h5>
                        <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        ...
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="button" class="btn btn-primary">Save changes</button>
                    </div>
                </div>
            </div>
        </div>

    </div>



@endsection
