<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Địa chỉ phong bì</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 10px;
        }
        .page {
            width: 210mm;
            height: 297mm;
            page-break-after: always;
            padding: 5mm;
            box-sizing: border-box;
            display: flex;
            flex-direction: column;
        }
        .address-box {
            border: 1px dotted #ccc;
            margin: 5px 0;
            padding: 6px;
            height: 27mm;
            width: 98%; /* Takes up 2/3 of the page width */
            box-sizing: border-box;
            font-size: 12pt;
            line-height: 1.1;
        }
        h1 {
            width: 100%;
            text-align: center;
            font-size: 16pt;
            margin-bottom: 15px;
        }
        .sender {
            margin-bottom: 10px;
        }
        .receiver {
            margin-top: 10px;
        }
        @media print {
            body {
                padding: 0;
            }
            .page {
                margin: 0;
                border: none;
            }
        }
    </style>
</head>
<body>
    <?php
    $sender = "<b>Người gửi</b>: Công Ty Công nghệ Số Galaxy Vietnam; Địa chỉ: Số 54 Nguyễn Đổng Chi, Nam Từ Liêm, Hà Nội; ĐT: 09.04.04.3689";

    $addresses = [
        "Chị Dương  - 0944 480 231- Học viện Ngoại giao, 69. Phố Chùa Láng",

        "Chị Thắm, Số ĐT: 0392668661 , Phòng KTNV Công ty PHBCTW, Địa chỉ Số 5 Phạm Hùng, Mỹ Đình 2, Từ Liêm, Hà Nội",

        "A Khánh, Trường Cao đẳng Công nghệ và Nông Lâm Đông Bắc, Xã Minh Sơn – Huyện Hữu Lũng – Tỉnh Lạng Sơn, ĐT: 0984146134",

        "Anh Phong Lưu: 0989789306, Trường Trung cấp Dân tộc nội trú tỉnh Thái Nguyên, Tổ dân phố Ấm, phường Hồng Tiến, TP Phổ Yên, tỉnh Thái Nguyên",

        "Chị Thùy - 0989137269, 134 Mai Anh Tuấn, Đống Đa, Hà Nội",

        "Anh Thực - 0965668369, Số 62 Nguyễn Huy Tưởng- Thanh xuân, Hà Nội",

        "Ms Hoa 0974479642 Cty TNHH MTV CN An Vui. Tầng 4, Tháp 2, Tòa nhà Time Tower, số 35 Lê Văn Lương, Thanh Xuân, HN",

        "A.Ngọc -0904282833, Trung tâm Chiếu phím Quốc gia, 87 Láng Hạ, Ba Đình, Hà Nội"
    ];

    // Remove duplicates
    $addresses = array_unique($addresses);

    foreach ($addresses as $index => $address) {
        echo '<div class="page">';

        // Create multiple copies of the same address on one page
        // 8 copies per page (8 rows × 1 column)
        for ($i = 0; $i < 10; $i++) {
            echo '<div class="address-box">';
            echo '<div class="sender">' . $sender . '</div>';
            echo '<div class="receiver"><b>Người nhận</b>: ' . $address . '</div>';
            echo '</div>';
        }

        echo '</div>';
    }
    ?>
</body>
</html>
