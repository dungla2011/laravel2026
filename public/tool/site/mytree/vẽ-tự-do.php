<!DOCTYPE html>
<html>
<head>
    <title>Vẽ tự do MyTree</title>

    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>


    <style>
        #draw-frame {
            width: 100%;
            /*height: 600px;*/
            border-top: 1px solid #ccc;
            border-bottom: 1px solid #ccc;
        }
        .controls {
            /*margin: 10px 0;*/
        }
        button {
            margin: 5px;
            padding: 8px 15px;
        }
        *{
            font-family: Tahoma;
            margin: 0;
            padding: 0;
            border: 0;
            /*font-size: 95%;*/
            box-sizing: border-box;

        }

        .modal-dialog {
            max-width: 800px;
        }

        .modal-body ul {
            padding-left: 20px;
            margin-top: 10px;
        }
        .modal-body ul li {
            margin-bottom: 10px;
            font-size: 16px;
            line-height: 1.5;
        }

    </style>

    <script>

        function guide1() {
            $('#guideModal').modal('toggle');
        }

    </script>
</head>
<body>
<div class="controls" style="height: 50px; position: relative" >
    <div style="position: absolute; LEFT: 5px; padding: 10px 10px">
    <h4>
        MYTREE - VẼ TỰ DO
    </h4>
    </div>
        <div style="position: absolute; right: 5px">
    <button onclick="guide1()" style="background-color: royalblue; color: white">Hướng dẫn</button>
    </div>
</div>

<!-- Bootstrap Modal for .guide -->
<div class="modal fade" id="guideModal" tabindex="-1" role="dialog" aria-labelledby="guideModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="guideModalLabel">Hướng dẫn</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">

                <ul>
                    <li>Đây là Vùng Vẽ tự do, dùng cho trường hợp bạn muốn vẽ lại để in ấn, ... đưa thêm hình ảnh, xoay chiều, thay đổi kích thước tự do...</li>
                    <li>Các thông tin hiện tại chỉ gồm Tên và đường liên kết, chưa bổ xung thông tin chi tiết khác.</li>
                    <li>Các thay đổi ở đây là một bản riêng biệt, sẽ không ảnh hưởng đến dữ liệu gốc.</li>
                    <li>Hãy ấn File -> Save -> Where : chọn Device để lưu vào máy tính của bạn và có thể mở lại sau này.</li>
                    <li>
                        Hướng dẫn nhanh
                        <ul>
                            <li>
                                Phím Space + Chuột để xem các vùng
                            </li>
                            <li>Dùng chuột để kéo Vị trí các phần tử</li>
                        </ul>
                    </li>
                    <li>Xem thêm hướng dẫn tại đây</li>
                </ul>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Đóng</button>
            </div>
        </div>
    </div>
</div>


<!-- Nhúng draw.io trong iframe -->
<!--<iframe id="drawio-frame" src="https://draw.mytree.vn/?url=https://mytree.vn/tool/testing/draw.io.get_file.php"></iframe>-->
<!--<iframe id="draw-frame" src="https://mytree.vn:8333/?url=https://mytree.vn/tool/testing/draw.io.get_file.php?id=123"></iframe>-->
<iframe id="draw-frame" src="https://draw.mytree.vn"  sandbox="allow-scripts allow-downloads  allow-same-origin"></iframe>
<script>
    window.addEventListener('resize', resizeIframe);

    function resizeIframe() {
        console.log(" Resize ....");
        const controlsHeight = document.querySelector('.controls').offsetHeight;
        const windowHeight = window.innerHeight;
        const iframe = document.getElementById('draw-frame');
        iframe.style.height = (windowHeight - controlsHeight - 10) + 'px';
    }
    // Initial resize on page load
    resizeIframe();
</script>

<script>

</script>
</body>
</html>
