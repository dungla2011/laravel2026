<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Chọn Địa Chỉ</title>
    <!-- Stylesheet Choices.js -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/choices.js/public/assets/styles/choices.min.css" />
    <style>
        body {
            font-family: Arial, sans-serif;
            padding: 20px;
        }
        .select-container {
            margin-bottom: 20px;
        }
        label {
            display: block;
            margin-bottom: 5px;
            font-weight: bold;
        }
        select {
            width: 300px;
            padding: 8px;
            font-size: 16px;
        }
        /* Thêm kiểu cho nút Submit nếu cần */
        button {
            padding: 10px 20px;
            font-size: 16px;
            cursor: pointer;
        }
    </style>
</head>
<body>

<h1>Chọn Địa Chỉ Của Bạn</h1>

Chọn theo địa chỉ

<div class="select-container">
    <label for="city">Thành phố:</label>
    <select id="city" placeholder="Chọn Thành phố">
        <option value="">-- Chọn Thành phố --</option>
    </select>
</div>

<div class="select-container">
    <label for="district">Quận/Huyện:</label>
    <select id="district" placeholder="Chọn Quận/Huyện" disabled>
        <option value="">-- Chọn Quận/Huyện --</option>
    </select>
</div>

<div class="select-container">
    <label for="ward">Phường/Xã:</label>
    <select id="ward" placeholder="Chọn Phường/Xã" disabled>
        <option value="">-- Chọn Phường/Xã --</option>
    </select>
</div>

<div class="select-container">
    <label for="street">Đường phố:</label>
    <select id="street" placeholder="Chọn Đường phố" disabled>
        <option value="">-- Chọn Đường phố --</option>
    </select>
</div>

<!-- Nút Submit (Tùy chọn) -->
<button id="submit-btn">Submit</button>

<!-- Scripts Choices.js -->
<script src="https://cdn.jsdelivr.net/npm/choices.js/public/assets/scripts/choices.min.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', () => {
        // Khởi tạo Choices.js cho từng select
        const citySelect = new Choices('#city', {
            searchEnabled: true,
            placeholder: true,
            shouldSort: false
        });

        const districtSelect = new Choices('#district', {
            searchEnabled: true,
            placeholder: true,
            shouldSort: false
        });

        const wardSelect = new Choices('#ward', {
            searchEnabled: true,
            placeholder: true,
            shouldSort: false
        });

        const streetSelect = new Choices('#street', {
            searchEnabled: true,
            placeholder: true,
            shouldSort: false
        });

        // API base URL
        const API_BASE_URL = 'https://mytree.vn/api/don-vi-hanh-chinh/tree';

        // Cache để lưu trữ dữ liệu đã fetch
        const cache = {};

        // Bản đồ từ ID đến has_child
        const idToHasChild = {};

        // Hàm lấy dữ liệu từ API
        const fetchData = async (pid = 0) => {
            // Kiểm tra cache trước
            if (cache[pid]) {
                console.log(`Fetching data for pid=${pid} from cache`);
                return cache[pid];
            }

            try {
                const url = `${API_BASE_URL}?pid=${pid}`;
                console.log(`Fetching data from: ${url}`);

                if(pid === ''){
                    console.log(" -- PID Empty, so return Empty data");
                    return [];
                }


                const response = await fetch(url);
                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }
                const result = await response.json();
                if (result.code !== 1) {
                    throw new Error(`API error! code: ${result.code}`);
                }
                console.log(`Data fetched for pid=${pid}:`, result.payload);
                // Lưu vào cache
                cache[pid] = result.payload;
                // Lưu vào bản đồ idToHasChild
                result.payload.forEach(item => {
                    idToHasChild[item.id] = item.has_child;
                });
                return result.payload;
            } catch (error) {
                console.error('Error fetching data:', error);
                return [];
            }
        };

        // Hàm populate select
        const populateSelect = async (choicesInstance, pid = 0, defaultLabel = '-- Chọn --') => {
            // Hiển thị loading
            choicesInstance.clearChoices();
            choicesInstance.setChoices([{
                value: '',
                label: 'Đang tải...',
            }], 'value', 'label', false);
            choicesInstance.disable();

            const data = await fetchData(pid);
            // Xóa các lựa chọn hiện tại
            choicesInstance.clearChoices();
            // Thêm lựa chọn mặc định và chọn nó
            choicesInstance.setChoices([{
                value: '',
                label: defaultLabel,
                selected: true
            }], 'value', 'label', false);
            // Đảm bảo lựa chọn mặc định được chọn
            choicesInstance.setValue(['']);

            // Kiểm tra dữ liệu trả về
            if (!Array.isArray(data)) {
                console.error(`Data for pid=${pid} is not an array:`, data);
                return;
            }

            if (data.length === 0) {
                console.log(`No data found for pid=${pid}`);
                // Có thể thêm lựa chọn "Không có dữ liệu" nếu muốn
                return;
            }

            // Thêm các lựa chọn mới
            const choices = data.map(item => ({
                value: String(item.id), // Đảm bảo value là chuỗi
                label: item.name
                // Không disable các tùy chọn
            }));
            console.log(`Populating select with pid=${pid}:`, choices);
            choicesInstance.setChoices(choices, 'value', 'label', true);
            // Kích hoạt lại select nếu có lựa chọn
            if (choices.length > 0) { // Có ít nhất một lựa chọn khác là '-- Chọn --'
                choicesInstance.enable();
            } else {
                choicesInstance.disable();
            }
        };

        // Hàm reset các select cấp dưới
        const resetLowerSelects = (levels) => {
            levels.forEach(level => {
                level.clearChoices();
                level.disable();
                level.setChoices([{
                    value: '',
                    label: `-- Chọn ${capitalizeFirstLetter(level.passedElement.element.getAttribute('id'))} --`,
                }], 'value', 'label', false);
                level.setValue(['']); // Đảm bảo lựa chọn mặc định được chọn và hiển thị
            });
        };

        // Hàm để viết hoa chữ cái đầu của một từ
        const capitalizeFirstLetter = (string) => {
            return string.charAt(0).toUpperCase() + string.slice(1);
        };

        // Tải dữ liệu cho Thành phố khi tải trang
        populateSelect(citySelect, 0, '-- Chọn Thành phố --');

        // Sự kiện khi thay đổi Thành phố
// Sự kiện khi thay đổi Thành phố
        citySelect.passedElement.element.addEventListener('change', async (event) => {
            const cityId = event.target.value;
            console.log('Thành phố được chọn:', cityId);
            // Reset tất cả các select cấp dưới
            resetLowerSelects([districtSelect, wardSelect, streetSelect]);
            if (cityId) {
                await populateSelect(districtSelect, cityId, '-- Chọn Quận/Huyện --');

                // Kiểm tra và cập nhật wardSelect nếu cần
                const districtId = districtSelect.getValue(true);
                // if (districtId && idToHasChild[districtId] === 1)
                {
                    await populateSelect(wardSelect, districtId, '-- Chọn Phường/Xã --');

                    // Kiểm tra và cập nhật streetSelect nếu cần
                    const wardId = wardSelect.getValue(true);
                    // if (wardId && idToHasChild[wardId] === 1)
                    {
                        await populateSelect(streetSelect, wardId, '-- Chọn Đường phố --');
                    }
                }
            }
        });


        // Sự kiện khi thay đổi Thành phố
        citySelect.passedElement.element.addEventListener('change', async (event) => {
            const cityId = event.target.value;
            console.log('Thành phố được chọn:', cityId);
            // Reset tất cả các select cấp dưới
            resetLowerSelects([districtSelect, wardSelect, streetSelect]);

            if (cityId) {
                await populateSelect(districtSelect, cityId, '-- Chọn Quận/Huyện --');
                // Disable các cấp dưới
                wardSelect.disable();
                streetSelect.disable();
            }
        });

// Sự kiện khi thay đổi Quận/Huyện
        districtSelect.passedElement.element.addEventListener('change', async (event) => {
            const districtId = event.target.value;
            console.log('Quận/Huyện được chọn:', districtId);
            // Reset các select cấp dưới
            resetLowerSelects([wardSelect, streetSelect]);

            if (districtId) {
                // if (idToHasChild[districtId] === 1) {
                await populateSelect(wardSelect, districtId, '-- Chọn Phường/Xã --');
                // Disable các cấp dưới
                streetSelect.disable();
                // }
            }
        });

// Sự kiện khi thay đổi Phường/Xã
        wardSelect.passedElement.element.addEventListener('change', async (event) => {
            const wardId = event.target.value;
            console.log('Phường/Xã được chọn:', wardId);
            // Reset các select cấp dưới
            resetLowerSelects([streetSelect]);

            if (wardId) {
                // if (idToHasChild[wardId] === 1) {
                await populateSelect(streetSelect, wardId, '-- Chọn Đường phố --');
                // }
            }
        });

        // Sự kiện khi thay đổi Quận/Huyện
        districtSelect.passedElement.element.addEventListener('change', async (event) => {
            const districtId = event.target.value;
            console.log('Quận/Huyện được chọn:', districtId);
            if (districtId) {
                // Vô hiệu hóa và dọn sạch các select cấp dưới
                resetLowerSelects([wardSelect, streetSelect]);
                if (idToHasChild[districtId] === 1) {
                    // Nếu Quận/Huyện có con, tải Phường/Xã
                    await populateSelect(wardSelect, districtId, '-- Chọn Phường/Xã --');
                } else {
                    // Nếu Quận/Huyện không có con, giữ Phường/Xã và Đường phố ở trạng thái disabled và clear
                    resetLowerSelects([wardSelect, streetSelect]);
                }
            } else {
                // Nếu không chọn Quận/Huyện, vô hiệu hóa và dọn sạch các select cấp dưới
                resetLowerSelects([wardSelect, streetSelect]);
            }
        });

        // Sự kiện khi thay đổi Phường/Xã
        wardSelect.passedElement.element.addEventListener('change', async (event) => {
            const wardId = event.target.value;
            console.log('Phường/Xã được chọn:', wardId);
            if (wardId) {
                // Vô hiệu hóa và dọn sạch Đường phố
                resetLowerSelects([streetSelect]);
                if (idToHasChild[wardId] === 1) {
                    // Nếu Phường/Xã có con, tải Đường phố
                    await populateSelect(streetSelect, wardId, '-- Chọn Đường phố --');
                } else {
                    // Nếu Phường/Xã không có con, giữ Đường phố ở trạng thái disabled và clear
                    resetLowerSelects([streetSelect]);
                }
            } else {
                // Nếu không chọn Phường/Xã, vô hiệu hóa và dọn sạch Đường phố
                resetLowerSelects([streetSelect]);
            }
        });

        // Sự kiện khi thay đổi Đường phố (nếu cần xử lý thêm)
        streetSelect.passedElement.element.addEventListener('change', (event) => {
            const streetId = event.target.value;
            console.log('Đường phố được chọn:', streetId);
            // Xử lý nếu cần thiết khi người dùng chọn Đường phố
        });

        // Hàm Validate Địa Chỉ (Tùy Chọn)
        const validateAddress = () => {
            const city = citySelect.getValue(true);
            const district = districtSelect.getValue(true);
            const ward = wardSelect.getValue(true);
            const street = streetSelect.getValue(true);

            if (!city || !district || (idToHasChild[district] === 1 && !ward) || (idToHasChild[ward] === 1 && !street)) {
                alert('Vui lòng chọn đầy đủ địa chỉ.');
                return false;
            }
            // Tiếp tục xử lý (ví dụ: gửi form)
            alert('Địa chỉ đã được chọn đầy đủ.');
            return true;
        };

        // Thêm sự kiện cho nút Submit (Tùy Chọn)
        document.getElementById('submit-btn').addEventListener('click', (e) => {
            e.preventDefault();
            validateAddress();
        });
    });
</script>
</body>
</html>
