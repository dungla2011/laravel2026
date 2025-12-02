<?php

$uri = \LadLib\Common\UrlHelper1::getFullUrl();

//echo "<pre> >>> " . __FILE__ . "(" . __LINE__ . ")<br/>";
//print_r($_SERVER);
//echo "</pre>";
$uri =  strip_tags(explode("?", request()->uri())[0]);
if (request()->is('api/*')){
?>
<br>
Chào {{ auth()->user() && auth()->user()->email ? auth()->user()->email : 'guest' }} <br>
Bạn không có quyền truy cập vùng này. {{$uri}}! No permission (Api)!
<?php
}
else{
?>
<meta name='viewport' content='width=device-width, initial-scale=1'>
<title> No permission </title>
<h3 style="background-color: lavender; padding: 20px; max-width: 500px;  text-align: center; margin: 50px auto; font-family: Calibri">


    <?php
    if(!$user = getUserIdCurrent_(1)){
        ?>
        <script type="text/javascript">
            window.location = "/login";
        </script>
    <?php
    ?>
    Bạn chưa đăng nhập
    <br>
    <a style="text-decoration: none;" href="/login"> Đăng nhập</a> | <a style="text-decoration: none;" href="/">Trang chủ</a>
    <?php
    }
    else{

    ?>
    Chào {{ $user ? $user->email : 'guest' }}
        <br>
        Tài khoản của bạn thuộc nhóm: <span style="color: red"> <?php
            $str = '';
            $mId = [];

            if(!$user->_roles || !$user->_roles->count()){
                echo "Chưa đăng ký thành công !";
            }
            else{

            if($user->_roles)
                foreach ($user->_roles AS $role){
                    if(in_array($role->id, $mId))
                        continue;
                    $mId[] = $role->id;
                    $str .= " " . $role['display_name'] . " | ";
                }
            echo trim($str, " |");

                if(!$str){

                }
           }

            ?>
            </span>

    <br>
    Bạn không có quyền truy cập vùng này..
    <?php
//    echo "<pre> >>> " . __FILE__ . "(" . __LINE__ . ")<br/>";
//    print_r($_SERVER);
//    echo "</pre>";
    ?>
    <br>
    '{{$uri}}'
    <br> (No permission)
        <br>
        <br>
        <a href="/">Trở lại</a> |
        <a style="text-decoration: none;" href="/member">Thành viên</a> | <a style="text-decoration: none;" href="/logout">Đăng xuất</a>
    <?php
    }
    ?>

</h3>
<?php
}
?>
