<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Chọn Cấp</title>
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
        button {
            padding: 10px 20px;
            font-size: 16px;
            cursor: pointer;
        }
    </style>
</head>
<body>
<h1>Chọn Phân Cấp</h1>


Chọn theo phân cấp không còn chỉ là địa chỉ nữa

<div class="select-container">
    <label for="level1">Cấp 1:</label>
    <select id="level1" placeholder="Chọn Cấp 1">
        <option value="">-- Chọn Cấp 1 --</option>
    </select>
</div>

<div class="select-container">
    <label for="level2">Cấp 2:</label>
    <select id="level2" placeholder="Chọn Cấp 2" disabled>
        <option value="">-- Chọn Cấp 2 --</option>
    </select>
</div>

<div class="select-container">
    <label for="level3">Cấp 3:</label>
    <select id="level3" placeholder="Chọn Cấp 3" disabled>
        <option value="">-- Chọn Cấp 3 --</option>
    </select>
</div>

<div class="select-container">
    <label for="level4">Cấp 4:</label>
    <select id="level4" placeholder="Chọn Cấp 4" disabled>
        <option value="">-- Chọn Cấp 4 --</option>
    </select>
</div>

<!-- Nút Submit (Tùy chọn) -->
<button id="submit-btn">Submit</button>

<!-- Scripts Choices.js -->
<script src="https://cdn.jsdelivr.net/npm/choices.js/public/assets/scripts/choices.min.js"></script>
<script>

    document.addEventListener('DOMContentLoaded', () => {
        // Khởi tạo Choices.js cho từng select
        const level1Select = new Choices('#level1', {
            searchEnabled: true,
            placeholder: true,
            shouldSort: false
        });

        const level2Select = new Choices('#level2', {
            searchEnabled: true,
            placeholder: true,
            shouldSort: false
        });

        const level3Select = new Choices('#level3', {
            searchEnabled: true,
            placeholder: true,
            shouldSort: false
        });

        const level4Select = new Choices('#level4', {
            searchEnabled: true,
            placeholder: true,
            shouldSort: false
        });

        // API base URL (thay đổi URL API của bạn ở đây)
        const API_BASE_URL = 'https://mytree.vn/api/don-vi-hanh-chinh/tree';

        // Cache để lưu trữ dữ liệu đã fetch
        const cache = {};

        // Bản đồ từ ID đến has_child
        const idToHasChild = {};

        // Hàm lấy dữ liệu từ API
        const fetchData = async (pid = 0) => {
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
                cache[pid] = result.payload;
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
        const populateSelect = async (choicesInstance, pid = 0, level = 1) => {
            const defaultLabel = `-- Chọn Cấp ${level} --`;
            choicesInstance.clearChoices();
            choicesInstance.setChoices([{
                value: '',
                label: 'Đang tải...',
            }], 'value', 'label', false);
            choicesInstance.disable();

            const data = await fetchData(pid);
            choicesInstance.clearChoices();
            choicesInstance.setChoices([{
                value: '',
                label: defaultLabel,
                selected: true
            }], 'value', 'label', false);
            choicesInstance.setValue(['']);

            if (!Array.isArray(data)) {
                console.error(`Data for pid=${pid} is not an array:`, data);
                return;
            }

            if (data.length === 0) {
                console.log(`No data found for pid=${pid}`);
                return;
            }

            const choices = data.map(item => ({
                value: String(item.id),
                label: item.name
            }));
            console.log(`Populating select with pid=${pid}:`, choices);

            choicesInstance.setChoices(choices, 'value', 'label', true);

            if (choices.length > 0) {
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
                const levelNumber = level.passedElement.element.id.replace('level', '');
                level.setChoices([{
                    value: '',
                    label: `-- Chọn Cấp ${levelNumber} --`,
                }], 'value', 'label', false);
                level.setValue(['']);
            });
        };

        // Tải dữ liệu cho Cấp 1 khi tải trang
        populateSelect(level1Select, 0, 1);


        level1Select.passedElement.element.addEventListener('change', async (event) => {
            const level1Id = event.target.value;
            console.log('Cấp 1 được chọn:', level1Id);
            resetLowerSelects([level2Select, level3Select, level4Select]);
            if (level1Id) {
                await populateSelect(level2Select, level1Id, 2);

                // Tự động populate các cấp dưới
                const level2Id = level2Select.getValue(true);
                await populateSelect(level3Select, level2Id, 3);

                const level3Id = level3Select.getValue(true);
                await populateSelect(level4Select, level3Id, 4);
            }
        });

        // Sự kiện khi thay đổi Cấp 1
        level1Select.passedElement.element.addEventListener('change', async (event) => {
            const level1Id = event.target.value;
            console.log('Cấp 1 được chọn:', level1Id);
            resetLowerSelects([level2Select, level3Select, level4Select]);
            if (level1Id) {
                await populateSelect(level2Select, level1Id, 2);
                level3Select.disable();
                level4Select.disable();
            }
        });

        // Sự kiện khi thay đổi Cấp 2
        level2Select.passedElement.element.addEventListener('change', async (event) => {
            const level2Id = event.target.value;
            console.log('Cấp 2 được chọn:', level2Id);
            resetLowerSelects([level3Select, level4Select]);
            if (level2Id) {
                await populateSelect(level3Select, level2Id, 3);
                level4Select.disable();
            }
        });

        // Sự kiện khi thay đổi Cấp 3
        level3Select.passedElement.element.addEventListener('change', async (event) => {
            const level3Id = event.target.value;
            console.log('Cấp 3 được chọn:', level3Id);
            resetLowerSelects([level4Select]);
            if (level3Id) {
                await populateSelect(level4Select, level3Id, 4);
            }
        });
    });

</script>
</body>
</html>
