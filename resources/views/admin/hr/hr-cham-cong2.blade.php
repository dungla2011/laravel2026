<?php
clsConfigTimeFrame::$class_data = \App\Models\HrSampleTimeEvent::class;;
clsConfigTimeFrame::$class_meta = \App\Models\HrSampleTimeEvent_Meta::class;;
clsConfigTimeFrame::$time_frame_type = 'full';
clsConfigTimeFrame::$time_frame_range = 'day';
clsConfigTimeFrame::$cat1_field = 'cat1';
clsConfigTimeFrame::$title = 'Chấm công ngày';
?>


@if(request("date_only"))
    @include("admin.demo-api.time-frame-template")
@else
    @include("admin.demo-api.time-frame-template")
@endif
