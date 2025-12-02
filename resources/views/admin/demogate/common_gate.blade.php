<?php
?>

<div class="content-wrapper">
    <div class="content">
        <div class="container-fluid">
            <br>
            DEMO GATE
            <br>
            Bạn đang đăng nhập với user <b style="color: red"> {{ auth()->user() ? auth()->user()->email : 'guest'  }} </b>
            <br><br>
            <a href="{{route("admin.demogate.test1")}}"> Click link này, user dev được quyền truy cập</a>
            <br>
            <a href="{{route("admin.demogate.test2")}}"> Click link này, user dev KHÔNG được quyền truy cập</a>
        </div>
    </div>
</div>

