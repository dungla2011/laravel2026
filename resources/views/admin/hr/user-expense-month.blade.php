<?php
clsConfigTimeFrame::$class_data = \App\Models\HrUserExpense::class;;
clsConfigTimeFrame::$class_meta = \App\Models\HrUserExpense_Meta::class;;
clsConfigTimeFrame::$time_frame_type = 'one';
clsConfigTimeFrame::$time_frame_range = 'month';
clsConfigTimeFrame::$cat1_field = 'cat1';
clsConfigTimeFrame::$title = 'Chi phí Tháng';

?>

@if(request("date_only"))
    @include("admin.demo-api.time-frame-template")
@else
    @include("admin.demo-api.time-frame-template")
@endif
