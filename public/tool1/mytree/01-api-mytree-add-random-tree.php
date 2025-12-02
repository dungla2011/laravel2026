<?php

/**

- Nhiệm vụ: Tạo cây gia phả với N thành viên, M cấp
+ Nhập vào: số N phần tử, và số M là số cấp
+ Cấp dưới luôn có số thành viên nhiều hơn cấp trên
+ Họ các con cháu phải trùng với cha gốc (họ các vợ chồng con cháu thì có thể random khác đi)


- Đây là API add thành viên của cây gia phả:
+ POST vào https://v5.mytree.vn/api/member-tree-mng/add
+ Bearer Token lấy trong .evn: TOKEN_TEST_MYTREE_API

+ Trả về:
{
    "code": 1,
    "payload": "11218555540340736",
    "payloadEx": null,
    "message": " Add done 11218555540340736! "
}

Với id ở trong: "payload": "11218555540340736",

- Các trường Post
name:  Tên
parent_id: id của bố mẹ (=0 nghĩa là ở Gốc của Cây), con dâu, rể có cùng parent_id với vợ/chồng
married_with: id của vợ chồng
gender: giới tính (1: nam, 2: nữ)
child_of_second_married: id của bố/mẹ lẽ (ví dụ bố có vợ 2, và người này là con vợ 2, thì child_of_second_married của người này = id của vợ 2)

- Ví dụ,
+ Thêm gốc , là Nam, thì cần : name,  parent_id = 0, giả sử id thêm vào là id1
+ Thêm vợ cho Gốc: name,  parent_id = 0, married_with = id1, gender=2 (nữa)
... tương tự cho các nút sau

- hãy tạo mảng Đệm + Tên thôi, còn Họ sẽ Add vào sau (mảng họ random)
và họ các con cháu phải trùng với cha gốc (họ các vợ chồng con cháu thì có thể random khác đi)




 */

/**
 * Tạo cây gia phả với N thành viên, M cấp - PHIÊN BẢN SỬA LỖI
 * - Vợ chồng KHÔNG cùng parent_id
 * - Cấp dưới luôn có số thành viên nhiều hơn cấp trên
 * - Họ con cháu trùng với cha gốc, họ vợ chồng có thể khác
 */

// Đọc token từ file .env
function loadEnv($filePath) {
    if (!file_exists($filePath)) {
        die("File .env không tồn tại!\n");
    }

    $lines = file($filePath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    $env = [];

    foreach ($lines as $line) {
        if (strpos($line, '=') !== false && !str_starts_with($line, '#')) {
            list($key, $value) = explode('=', $line, 2);
            $env[trim($key)] = trim($value);
        }
    }

    return $env;
}

// Mảng họ nam (cho cha gốc và con cháu nam)
$surnames_male = [
    'Nguyễn', 'Trần', 'Lê', 'Phạm', 'Huỳnh', 'Hoàng', 'Phan', 'Vũ', 'Võ', 'Đặng',
    'Bùi', 'Đỗ', 'Hồ', 'Ngô', 'Dương', 'Lý', 'Đinh', 'Lương', 'Đào', 'Tô'
];

// Mảng họ nữ (cho vợ chồng)
$surnames_female = [
    'Nguyễn', 'Trần', 'Lê', 'Phạm', 'Huỳnh', 'Hoàng', 'Phan', 'Vũ', 'Võ', 'Đặng',
    'Bùi', 'Đỗ', 'Hồ', 'Ngô', 'Dương', 'Lý', 'Đinh', 'Lương', 'Đào', 'Tô',
    'Mai', 'Cao', 'Tăng', 'Chu', 'Kiều', 'La', 'Thái', 'Hà', 'Tạ', 'Quách'
];

// Mảng đệm nam
$middle_names_male = [
    'Văn', 'Đức', 'Thành', 'Minh', 'Quang', 'Hữu', 'Xuân', 'Hoàng', 'Bảo', 'Thế',
    'Công', 'Duy', 'Gia', 'Tuấn', 'Khắc', 'Ngọc', 'Tiến', 'Đình', 'Thanh', 'Việt'
];

// Mảng đệm nữ
$middle_names_female = [
    'Thị', 'Ngọc', 'Thu', 'Thanh', 'Mai', 'Hồng', 'Diệu', 'Phương', 'Bích', 'Kim',
    'Như', 'Thúy', 'Xuân', 'Hạnh', 'Ánh', 'Linh', 'Lan', 'Hoa', 'Hương', 'Yến'
];

// Mảng tên nam
$first_names_male = [
    'Hưng', 'Dũng', 'Thắng', 'Phong', 'Hùng', 'Long', 'Tùng', 'Kiên', 'Cường', 'Sơn',
    'Nam', 'Đạt', 'Thái', 'Hải', 'Anh', 'Khoa', 'Bình', 'Trung', 'Hiếu', 'Hoàng',
    'Vũ', 'Toàn', 'Tuấn', 'Lâm', 'Nghĩa', 'Hạnh', 'Trí', 'Đức', 'Quân', 'Vinh'
];

// Mảng tên nữ
$first_names_female = [
    'Lan', 'Hoa', 'Linh', 'Anh', 'Thảo', 'Ngọc', 'Hương', 'Mai', 'Trang', 'Hằng',
    'Thu', 'Liên', 'Hạnh', 'Yến', 'Tuyết', 'Phương', 'Nhung', 'Thúy', 'Loan', 'Hồng',
    'Vân', 'Xuân', 'Dung', 'Giang', 'My', 'Thùy', 'Nga', 'Oanh', 'Hiền', 'Minh'
];

// Hàm tạo tên đầy đủ - SỬA LOGIC HỌ CHO CON CÁI
function generateFullName($gender, $surname = null, $useFamilySurname = true, $parent_gender = null, $spouse_surname = null) {
    global $surnames_male, $surnames_female, $middle_names_male, $middle_names_female, $first_names_male, $first_names_female, $family_surname;

    // Xác định họ theo logic Việt Nam
    if ($surname) {
        // Đã chỉ định họ cụ thể
        $final_surname = $surname;
    } elseif ($useFamilySurname && isset($family_surname)) {
        // Logic truyền họ:
        if ($parent_gender === 2 && $spouse_surname) {
            // Nếu cha mẹ là nữ (con gái lấy chồng) → con mang họ chồng
            $final_surname = $spouse_surname;
        } else {
            // Cha là nam hoặc trường hợp khác → con mang họ dòng gia đình
            $final_surname = $family_surname;
        }
    } else {
        // Random họ
        if ($gender == 1) {
            $final_surname = $surnames_male[array_rand($surnames_male)];
        } else {
            $final_surname = $surnames_female[array_rand($surnames_female)];
        }
    }

    if ($gender == 1) { // Nam
        $middle = $middle_names_male[array_rand($middle_names_male)];
        $first = $first_names_male[array_rand($first_names_male)];
    } else { // Nữ
        $middle = $middle_names_female[array_rand($middle_names_female)];
        $first = $first_names_female[array_rand($first_names_female)];
    }

    return "$final_surname $middle $first";
}

// Hàm gọi API thêm thành viên
function addMember($name, $parent_id = 0, $married_with = null, $gender = 1, $child_of_second_married = null) {
    global $token;

    $url = 'https://v5.mytree.vn/api/member-tree-mng/add';

    $data = [
        'name' => $name,
        'parent_id' => $parent_id,
        'gender' => $gender
    ];

    if ($married_with) {
        $data['married_with'] = $married_with;
    }

    if ($child_of_second_married) {
        $data['child_of_second_married'] = $child_of_second_married;
    }

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Authorization: Bearer ' . $token,
        'Content-Type: application/x-www-form-urlencoded'
    ]);

    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    if ($httpCode == 200) {
        $result = json_decode($response, true);
        if ($result && $result['code'] == 1) {
            echo "✓ Thêm thành công: {$name} (ID: {$result['payload']})\n";
            return $result['payload'];
        }
    }

    echo "✗ Lỗi thêm: {$name} - Response: {$response}\n";
    return null;
}

// Hàm tính số thành viên cho mỗi cấp
function calculateMembersPerLevel($total_members, $levels) {
    $members_per_level = [];

    // Cấp 1: 2 người (ông bà gốc)
    $members_per_level[0] = 2;
    $remaining = $total_members - 2;

    // Phân phối tăng dần cho các cấp còn lại
    $total_ratio = 0;
    for ($i = 1; $i < $levels; $i++) {
        $total_ratio += $i; // 1 + 2 + 3 + ... + (levels-1)
    }

    for ($i = 1; $i < $levels; $i++) {
        if ($i == $levels - 1) {
            // Cấp cuối: tất cả còn lại
            $members_per_level[$i] = $remaining;
        } else {
            $ratio = $i / $total_ratio;
            $count = max(1, intval($remaining * $ratio));
            $members_per_level[$i] = $count;
            $remaining -= $count;
        }
    }

    return $members_per_level;
}

// Hàm main
function createFamilyTree($total_members, $levels) {
    global $family_surname, $token;

    echo "=== BẮT ĐẦU TẠO CÂY GIA PHẢ (LOGIC HỌ ĐÚNG) ===\n";
    echo "Tổng số thành viên: $total_members\n";
    echo "Số cấp: $levels\n";
    echo "Logic họ: Con trai → con mang họ cha | Con gái lấy chồng → con mang họ chồng\n\n";

    // Tính số thành viên mỗi cấp
    $members_per_level = calculateMembersPerLevel($total_members, $levels);

    echo "Phân phối thành viên theo cấp:\n";
    for ($i = 0; $i < $levels; $i++) {
        echo "- Cấp " . ($i + 1) . ": {$members_per_level[$i]} người\n";
    }
    echo "\n";

    $tree = []; // Lưu trữ cấu trúc cây [level][member_info]
    $current_generation = []; // Thế hệ hiện tại có thể sinh con

    // Tạo họ gia đình (từ cha gốc)
    $family_surname = $GLOBALS['surnames_male'][array_rand($GLOBALS['surnames_male'])];
    echo "Họ gia đình: $family_surname\n\n";

    for ($level = 0; $level < $levels; $level++) {
        echo "--- CẤP " . ($level + 1) . " ---\n";
        $level_members = [];

        if ($level == 0) {
            // Cấp gốc: Tạo ông bà
            $grandpa_name = generateFullName(1, $family_surname);
            $grandpa_id = addMember($grandpa_name, 0, null, 1);

            if ($grandpa_id) {
                $grandma_name = generateFullName(2, null, false); // Bà có thể có họ khác
                $grandma_id = addMember($grandma_name, 0, $grandpa_id, 2);

                if ($grandma_id) {
                    $grandma_surname = explode(' ', $grandma_name)[0]; // Lấy họ của bà

                    $level_members[] = [
                        'id' => $grandpa_id,
                        'name' => $grandpa_name,
                        'gender' => 1,
                        'spouse_id' => $grandma_id,
                        'is_blood_relative' => true,
                        'surname' => $family_surname,
                        'spouse_surname' => $grandma_surname
                    ];
                    $level_members[] = [
                        'id' => $grandma_id,
                        'name' => $grandma_name,
                        'gender' => 2,
                        'spouse_id' => $grandpa_id,
                        'is_blood_relative' => false, // Vợ không phải dòng máu
                        'surname' => $grandma_surname,
                        'spouse_surname' => $family_surname
                    ];

                    // Chỉ người có dòng máu mới có thể sinh con
                    $current_generation = [
                        [
                            'id' => $grandpa_id,
                            'name' => $grandpa_name,
                            'gender' => 1,
                            'surname' => $family_surname,
                            'spouse_surname' => $grandma_surname
                        ]
                    ];
                }
            }
        } else {
            // Các cấp khác: Tạo con cháu
            $target_count = $members_per_level[$level];
            $created_count = 0;
            $new_generation = [];

            foreach ($current_generation as $parent) {
                if ($created_count >= $target_count) break;

                // Tìm thông tin cha mẹ từ cấp trước để lấy họ
                $parent_info = null;
                foreach ($tree[$level - 1] as $member) {
                    if ($member['id'] == $parent['id']) {
                        $parent_info = $member;
                        break;
                    }
                }

                // Xác định họ cho con cái
                $child_surname = $family_surname; // Mặc định
                if ($parent_info) {
                    if ($parent_info['gender'] == 2 && isset($parent_info['spouse_surname'])) {
                        // Nếu cha mẹ là nữ (con gái lấy chồng) → con mang họ chồng
                        $child_surname = $parent_info['spouse_surname'];
                    } else {
                        // Cha là nam → con mang họ cha
                        $child_surname = $parent_info['surname'];
                    }
                }

                // Số con cho mỗi cha/mẹ
                $remaining = $target_count - $created_count;
                $children_count = min(rand(1, 4), $remaining);

                for ($j = 0; $j < $children_count && $created_count < $target_count; $j++) {
                    // Tạo con với họ đúng
                    $child_gender = rand(1, 2);
                    $child_name = generateFullName($child_gender, $child_surname, false);
                    $child_id = addMember($child_name, $parent['id'], null, $child_gender);

                    if ($child_id) {
                        $child_data = [
                            'id' => $child_id,
                            'name' => $child_name,
                            'gender' => $child_gender,
                            'is_blood_relative' => true,
                            'surname' => $child_surname
                        ];

                        // Tạo vợ/chồng cho con (nếu không phải cấp cuối)
                        if ($level < $levels - 1 && $created_count + 1 < $target_count) {
                            $spouse_gender = $child_gender == 1 ? 2 : 1;
                            $spouse_name = generateFullName($spouse_gender, null, false);
                            $spouse_surname = explode(' ', $spouse_name)[0];

                            // Con dâu/rể có cùng parent_id với vợ/chồng (cùng thuộc về một gia đình)
                            $spouse_id = addMember($spouse_name, $parent['id'], $child_id, $spouse_gender);

                            if ($spouse_id) {
                                $child_data['spouse_id'] = $spouse_id;
                                $child_data['spouse_surname'] = $spouse_surname;
                                $level_members[] = [
                                    'id' => $spouse_id,
                                    'name' => $spouse_name,
                                    'gender' => $spouse_gender,
                                    'spouse_id' => $child_id,
                                    'is_blood_relative' => false,
                                    'surname' => $spouse_surname,
                                    'spouse_surname' => $child_surname
                                ];
                                $created_count++;
                            }
                        }

                        $level_members[] = $child_data;
                        $created_count++;

                        // Thêm vào thế hệ có thể sinh con (với đầy đủ thông tin họ)
                        if ($level < $levels - 1) {
                            $new_generation[] = [
                                'id' => $child_id,
                                'name' => $child_name,
                                'gender' => $child_gender,
                                'surname' => $child_surname,
                                'spouse_surname' => isset($child_data['spouse_surname']) ? $child_data['spouse_surname'] : null
                            ];
                        }
                    }
                }
            }

            $current_generation = $new_generation;
        }

        $tree[$level] = $level_members;
        echo "Đã tạo " . count($level_members) . " thành viên cho cấp " . ($level + 1) . "\n\n";
    }

    echo "=== HOÀN THÀNH TẠO CÂY GIA PHẢ ===\n";
    echo "Tổng số thành viên đã tạo: " . array_sum(array_map('count', $tree)) . "\n";

    return $tree;
}

// Chương trình chính
try {
    // Đọc token từ file .env
    $env = loadEnv('.env');
    $token = $env['TOKEN_TEST_MYTREE_API'] ?? null;

    if (!$token) {
        die("Không tìm thấy TOKEN_TEST_MYTREE_API trong file .env!\n");
    }

    // Nhập số lượng thành viên và số cấp
    echo "Nhập số lượng thành viên (N): ";
    $n = intval(trim(fgets(STDIN)));

    echo "Nhập số cấp (M): ";
    $m = intval(trim(fgets(STDIN)));

    if ($n < 2 || $m < 1) {
        die("Số thành viên phải >= 2 và số cấp phải >= 1!\n");
    }

    if ($n < $m * 2) {
        die("Số thành viên quá ít so với số cấp yêu cầu!\n");
    }

    // Tạo cây gia phả
    $tree = createFamilyTree($n, $m);

} catch (Exception $e) {
    echo "Lỗi: " . $e->getMessage() . "\n";
}

?>
