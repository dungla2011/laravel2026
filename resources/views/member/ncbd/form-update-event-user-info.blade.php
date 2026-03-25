@section("title")
    Event Update User Information
@endsection

<?php
use App\Models\EventUserInfo;
use Illuminate\Support\Facades\Validator;

$objU = getCurrentUserId(1);
$useFormPost = !$objU; // If no objU, use form POST instead of AJAX
$formAction = '';
$validationErrors = [];
$successMessage = '';

$phoneOK = $emailOK = '';
$isConfirmPhone = $isConfirmEmail = 0;

if(request('update_user_info') ){

    //Thêm cookie yc user nhập phone hoặc email
    $updateUI = dfh1b(request('update_user_info'));
    $updateUI = substr($updateUI, 0, 100);

    if(!$eventUserInfo = EventUserInfo::where('command_ex', $updateUI)->first()){
        die("Not valid user info!");
//        echo ("FOUND USER ... $eventUserInfo->id");
    }

    if($eventUserInfo instanceof EventUserInfo);

    $emailOK = $eventUserInfo->isHaveEmail();
    $phoneOK = $eventUserInfo->isHavePhone();
    $emailOK1 = explode("@", $emailOK)[0];


    //Post lên thì sẽ ghi vào cookie nếu đúng:


    if($confirm_email = request('confirm_email')){

        $setCookieOK = 0;
        if($confirm_email == $emailOK){
            $setCookieOK = 1;
            setcookie("confirm_email", $emailOK, time()+ 3600 * 24 * 99);
        }
    }
    if($confirm_phone = request('confirm_phone')){

        $setCookieOK = 0;
        if($confirm_phone == $phoneOK){
            $setCookieOK = 1;
            setcookie("confirm_phone", $confirm_phone, time()+ 3600 * 24 * 99);
        }
    }

    if($setCookieOK ?? 0)
    if(!$setCookieOK){
        bl("Giá trị nhập không đúng?");
//            return;
    }

    usleep(300000);

    //User sẽ confirm phone, email trước khi được post form
    if($emailOK)
    if($_COOKIE["confirm_email"] ?? ''){
        if($_COOKIE["confirm_email"] == $emailOK){
            //Đã xác thực email
            $isConfirmEmail = 1;
        }
    }
    if($phoneOK)
        if($_COOKIE["confirm_phone"] ?? ''){
            if($_COOKIE["confirm_phone"] == $emailOK){
                //Đã xác thực email
                $isConfirmPhone = 1;
            }
        }

}

// Handle form POST submission
if (request('update_user_info') && request()->isMethod('POST') && request('first_name')) {
    $input = request()->post();
//    echo "<pre> >>> " . __FILE__ . "(" . __LINE__ . ")<br/>";
//    print_r($input);
//    echo "</pre>";


    $timeUpdate = explode("-", $updateUI)[1];

//    die("TIME = $timeUpdate");

    if($timeUpdate < time() - 3600){
        die("Hết hạn update!");
    }

    // Initialize or get existing record
//    $recordId = $input['record_id'] ?? null;
//    $eventUserInfo = $recordId ? EventUserInfo::find($recordId) : new EventUserInfo();

    // Determine validation rules
    $rules = $eventUserInfo->getValidateRuleUpdate($eventUserInfo->id);

    // Merge with additional required rules for POST
    $rules = array_merge($rules, [
        'first_name' => 'required|string|max:100',
        'last_name' => 'required|string|max:100',
        'title' => 'nullable|string',
        'gender' => 'nullable|in:,1,2',
        'id_number' => 'nullable|string|max:20',
        'tax_number' => 'nullable|string|max:20',
        'organization' => 'nullable|string|max:255',
        'designation' => 'nullable|string|max:255',
        'address' => 'nullable|string',
        'bank_name_text' => 'nullable|string|max:100',
        'bank_acc_number' => 'nullable|string|max:50',
    ]);

    // Validate
    $validator = Validator::make($input, $rules);

    if ($validator->fails()) {
        $validationErrors = $validator->errors()->toArray();
    } else {
        try {
            // Fix phone number if provided
            if (!empty($input['phone'])) {
                $input['phone'] = EventUserInfo::fixPhoneNumber($input['phone']);
            }

            // Fill and save
            $eventUserInfo->fill($input)->save();

            if($eventUserInfo instanceof  EventUserInfo);
            unset($input['token']);
            unset($input['_token']);
            $eventUserInfo->updateChangeLog($input);

            $successMessage = $eventUserInfo->id ? 'Cập nhật thông tin thành công!' : 'Thêm thông tin thành công!';
        } catch (\Exception $e) {
            $validationErrors['system'] = ['Lỗi hệ thống: ' . $e->getMessage()];
        }
    }
}
?>

<div class="mt-2 form_update_event_user_info"
    style="border-bottom:  1px solid #ccc; margin: 0px auto ;max-width: 1200px ; background-color: #eee; padding : 30px; box-shadow : 0px 0px 5px 1px #6f6d6dab;  border-radius: 10px" data-code-pos='ppp17698573676521'>

    <h4 class="mt-1 mb-4 text-center" style="border-bottom: 0px solid #ccc; padding-bottom: 10px;   border-radius: 10px"><i class="fa fa-fw fa-user-circle"></i>
        Cập nhật Thông tin cá nhân
    </h4>

    <?php
    if(request('update_user_info') && !$isConfirmPhone && !$isConfirmEmail){

        ?>

    <form id="confirmEmailOrUser" class="text-center" method="POST" style="margin: 0 auto">
        <div class="form-group text-center col-md-5" style="margin: 0 auto">
            @if($emailOK)
                @csrf
            <label for="confirm_email">Quý khách vui lòng xác nhận email đã đăng ký sự kiện để tiếp tục<span class="text-danger">*</span></label>
            <input type="text" class="form-control mt-1 text-center" placeholder="Nhập vào email: {{  substr($emailOK1, 0, 1) . '...' . substr($emailOK1, -1)  }}@..." id="confirm_email" name="confirm_email"
                   value="{{ request('confirm_email') }}" required>
            @elseif($phoneOK)
                <label for="confirm_phone">Quý khách vui lòng xác nhận số phone đã đăng ký sự kiện để tiếp tục<span class="text-danger">*</span></label>
                <input type="text" class="form-control mt-1 text-center" placeholder="Nhập vào số phone: {{ substr($phoneOK, 0, 4) . '...'  }} ..." id="confirm_phone" name="confirm_phone"
                       value="{{ request('confirm_phone') }}" required>

                <input type="hidden" name="confirm_phone_email">
            @endif
            <button type="submit" class="btn btn-primary btn-sm mt-3"> Xác nhận </button>
        </div>

    </form>


<?php
    }
    else{
    ?>

    <form id="updateUserInfoForm" method="POST" onsubmit="return true;">
        @if(request('update_user_info'))
            @csrf

            @if($successMessage)
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <i class="fa fa-check-circle"></i> {{ $successMessage }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            @if($validationErrors)
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <i class="fa fa-exclamation-circle"></i> <strong>Có lỗi xảy ra:</strong>
                    <ul class="mb-0 mt-2">
                        @foreach($validationErrors as $field => $errors)
                            @foreach($errors as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        @endforeach
                    </ul>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif
        @endif
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
                        value="<?= $email ?>" style="background-color: #f5f5f5;">
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
            <div class="form-group col-md-6">
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

            <div class="form-group col-md-6">
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
            <div id="updateMessage" class="mt-1"></div>
            <button type="button" class="btn btn-primary" id="btnUpdateInfo">
                <i class="fa fa-save"></i> Cập nhật thông tin
            </button>

        </div>
    </form>

    <?php
    }
    ?>
</div>

<script>
    window.addEventListener('DOMContentLoaded', function() {

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
            const useFormPost = <?= $useFormPost ? 'true' : 'false' ?>;

            // If using form POST, just submit the form
            if (useFormPost) {
                $btn.prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i> Đang cập nhật...');
                $form.submit();
                return;
            }

            // Otherwise use AJAX (existing JWT token code)
            $btn.prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i> Đang cập nhật...');
            $msg.html('');

            const formData = $form.serializeArray();
            const data = {};
            formData.forEach(item => {
                // Skip search_terms field added by Choices.js and CSRF token
                if (item.name !== 'search_terms' && item.name !== '_token' && item.name !== 'record_id') {
                    data[item.name] = item.value;
                }
            });

            // Get user token
            const token = "<?php echo $objU?->getJWTUserToken(); ?>";

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
                    $msg.html('<div class="alert1 alert-success1 my-2" style="color: green"><i class="fa fa-check-circle"></i> Cập nhật thông tin thành công!</div>');
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
    });

</script>
